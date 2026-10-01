import requests

def fetch_data(category=None):
    url = "http://localhost:8000/api.php"
    params = {"category": category} if category else {}
    
    try:
        response = requests.get(url, params=params)
        data = response.json()
        
        if response.status_code != 200:
            print(f"Помилка {response.status_code}: {data.get('error')}")
            return

        if not category:
            print("Доступні категорії:")
            for cat in data.get("categories", []):
                print(f" - {cat}")
            print(f"Підказка: {data.get('hint')}")
        else:
            print(f"Обладнання в категорії '{data.get('category')}':")
            for device in data.get("devices", []):
                print(f" - {device['name']} (Інтерфейс: {device['protocol']})")
                
    except requests.exceptions.RequestException as e:
        print(f"Помилка з'єднання з API: {e}")

if __name__ == "__main__":
    print("--- Запит без параметрів ---")
    fetch_data()
    
    print("\n--- Запит існуючої категорії ---")
    fetch_data("Temperature")
    
    print("\n--- Запит з помилкою ---")
    fetch_data("Unknown")