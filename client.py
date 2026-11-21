import json
import requests

BASE_URL = "http://127.0.0.1:8000/api"


def fetch_json(url, params=None):
    resp = requests.get(url, params=params, timeout=5)
    print(f"URL: {resp.url} | HTTP {resp.status_code}")
    try:
        return resp.status_code, resp.json()
    except ValueError:
        print(f"Сервер повернув не JSON:\n{resp.text[:300]}")
        return resp.status_code, None


def main():
    try:
        print("--- Запит без параметрів (категорії) ---")
        _, cat_data = fetch_json(f"{BASE_URL}/categories")
        if cat_data is not None:
            print(json.dumps(cat_data, ensure_ascii=False, indent=2))

        first_code = "Temperature"
        if cat_data and cat_data.get("categories"):
            first_code = cat_data["categories"][0]["code"]

        print(f"\n--- Запит існуючої категорії ({first_code}) ---")
        _, by_cat = fetch_json(f"{BASE_URL}/devices/{first_code}")
        if by_cat is not None:
            print(json.dumps(by_cat, ensure_ascii=False, indent=2))

        print("\n--- Запит усіх пристроїв ---")
        _, all_devices = fetch_json(f"{BASE_URL}/devices")
        if all_devices is not None:
            print(json.dumps(all_devices, ensure_ascii=False, indent=2))

        print("\n--- Запит з помилкою (неіснуюча категорія) ---")
        _, err_data = fetch_json(f"{BASE_URL}/devices/NonExistentCategory")
        if err_data is not None:
            print(json.dumps(err_data, ensure_ascii=False, indent=2))

    except requests.RequestException as e:
        print(f"Помилка з'єднання з API: {e}")


if __name__ == "__main__":
    main()