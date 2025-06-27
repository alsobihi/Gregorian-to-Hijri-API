# Gregorian-to-Hijri-API
PHP Gregorian to Hijri API




````markdown
# Gregorian to Hijri Date Converter API

This is a simple PHP API that converts a date from the Gregorian calendar to the Hijri (Islamic) calendar. It takes a date in `dd/mm/yyyy` format and returns the corresponding Hijri date in several formats.

---

## API Details

* **Endpoint:** `/hijri_converter_api.php`
* **Method:** `POST`

---

## Request Body

The API expects a JSON object in the request body with a single key, `date`.

**Example:**
```json
{
  "date": "27/07/2025"
}
````

-----

## Responses

### ✅ Success Response (`200 OK`)

If the date is provided in the correct format and is a valid Gregorian date, the API will return a `200 OK` status and a JSON object with the converted dates.

**Example Payload:**

```json
{
    "status": "success",
    "message": "Date converted successfully.",
    "data": {
        "gregorian_date": "27/07/2025",
        "hijri_date": {
            "numeric": "1447-02-02",
            "long_arabic": "٢ صفر، ١٤٤٧",
            "full_arabic": "الأحد، ٢ صفر، ١٤٤٧ هـ"
        }
    }
}
```

### ❌ Error Responses (`400 Bad Request`)

If there is an issue with the request, the API will return a `400 Bad Request` status and a JSON object with a specific error message.

**1. Date Not Provided:**

```json
{
    "status": "error",
    "message": "No date provided. Please send a JSON payload with a 'date' key. e.g., {\"date\": \"27/07/2025\"}"
}
```

**2. Invalid Date Format:**

```json
{
    "status": "error",
    "message": "Invalid date format. Please use 'dd/mm/yyyy'."
}
```

**3. Invalid Gregorian Date (e.g., day 32):**

```json
{
    "status": "error",
    "message": "Could not parse the provided date. Make sure it is a valid Gregorian date."
}
```

-----

## How to Use (cURL Example)

You can test the API from your command line using `curl`.

```bash
curl -X POST \
  [http://your-domain.com/hijri_converter_api.php](http://your-domain.com/hijri_converter_api.php) \
  -H 'Content-Type: application/json' \
  -d '{"date": "27/07/2025"}'
```
