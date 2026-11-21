import getpass
import re
from html.parser import HTMLParser
from urllib.parse import urljoin

import requests

BASE_URL = "http://127.0.0.1:8000"


class LoginFormParser(HTMLParser):
    """Знаходить action форми входу, приховані поля (CSRF) та імена полів логіна/пароля."""

    def __init__(self):
        super().__init__()
        self.in_form = False
        self.form_found = False
        self.action = ""
        self.hidden_inputs = {}
        self.username_field = "_username"
        self.password_field = "_password"

    def handle_starttag(self, tag, attrs):
        attrs_dict = dict(attrs)
        if tag == "form" and not self.form_found:
            self.in_form = True
            self.form_found = True
            self.action = attrs_dict.get("action", "")
        elif tag == "input" and self.in_form:
            name = attrs_dict.get("name")
            if not name:
                return
            input_type = attrs_dict.get("type", "text").lower()
            value = attrs_dict.get("value", "")
            if input_type == "hidden":
                self.hidden_inputs[name] = value
            elif input_type in ("email", "text") and "csrf" not in name.lower():
                self.username_field = name
            elif input_type == "password":
                self.password_field = name

    def handle_endtag(self, tag):
        if tag == "form" and self.in_form:
            self.in_form = False


def extract_title(html: str) -> str:
    match = re.search(r"<title>(.*?)</title>", html, re.IGNORECASE | re.DOTALL)
    return re.sub(r"\s+", " ", match.group(1)).strip() if match else "(без заголовка)"


def extract_error(html: str) -> str:
    match = re.search(r'<div[^>]*alert-danger[^>]*>(.*?)</div>', html, re.IGNORECASE | re.DOTALL)
    if not match:
        return "не вдалося визначити текст помилки"
    text = re.sub(r"<[^>]+>", "", match.group(1))
    return re.sub(r"\s+", " ", text).strip()


def main():
    session = requests.Session()
    admin_url = f"{BASE_URL}/admin"
    login_url = f"{BASE_URL}/login"

    try:
        # 1. Перевірка захисту /admin без авторизації
        print("--- 1. Запит до /admin без сесії ---")
        unauth_resp = session.get(admin_url, allow_redirects=False, timeout=5)
        print(f"HTTP {unauth_resp.status_code} | Location: {unauth_resp.headers.get('Location')}")

        # 2. Завантаження форми входу та отримання CSRF-токена
        print("\n--- 2. Отримання форми входу та CSRF-токена ---")
        login_page = session.get(login_url, timeout=5)
        login_page.raise_for_status()

        parser = LoginFormParser()
        parser.feed(login_page.text)

        post_url = urljoin(login_url, parser.action)
        print(f"Форма входу: {post_url}")
        print(f"Поля: login='{parser.username_field}', password='{parser.password_field}'")
        print(f"Приховані поля (CSRF): {list(parser.hidden_inputs.keys())}")

        # 3. Введення облікових даних (пароль не зберігається в коді)
        print("\n--- 3. Авторизація через сесію ---")
        email = input("Email адміністратора [admin@scada.local]: ").strip() or "admin@scada.local"
        password = getpass.getpass("Пароль (приховане введення): ")

        payload = dict(parser.hidden_inputs)
        payload[parser.username_field] = email
        payload[parser.password_field] = password

        headers = {
            "Origin": BASE_URL,
            "Referer": login_url,
            "Sec-Fetch-Site": "same-origin",
        }

        auth_resp = session.post(post_url, data=payload, headers=headers, allow_redirects=True, timeout=5)
        if "/login" in auth_resp.url:
            print(f"Помилка входу. Відповідь Symfony: {extract_error(auth_resp.text)}")
            return

        print(f"Вхід успішний! Редірект на: {auth_resp.url}")
        print(f"Cookie сесії: {list(session.cookies.get_dict().keys())}")

        # 4. Доступ до захищеної сторінки /admin у межах активної сесії
        print("\n--- 4. Запит до захищеної адмін-панелі /admin ---")
        admin_resp = session.get(admin_url, allow_redirects=True, timeout=5)
        print(f"HTTP {admin_resp.status_code} | URL: {admin_resp.url}")
        print(f"Заголовок сторінки: {extract_title(admin_resp.text)}")

        has_panel = "SCADA Адмін-панель" in admin_resp.text
        has_user = email in admin_resp.text
        print(f"Знайдено інтерфейс EasyAdmin: {has_panel}")
        print(f"Відображається поточний адмін ({email}): {has_user}")

    except requests.RequestException as e:
        print(f"Помилка з'єднання з сервером: {e}")


if __name__ == "__main__":
    main()