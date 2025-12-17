# API Testing Guide (Postman / cURL)

## 1. Base & Headers
- Base URL (dev): `http://127.0.0.1:8000` (sesuaikan dengan port `php artisan serve`)
- Headers:
  - `Accept: application/json`
  - `Content-Type: application/json` (untuk POST)

## 2. Auth Flow (session cookie)
1) Register (opsional)  
   `POST /api/register`  
   Body:
   ```json
   {
     "name": "User Demo",
     "email": "user@example.com",
     "phone": "0812345678",
     "password": "password123",
     "password_confirmation": "password123"
   }
   ```
   Response: 201 + cookie `laravel_session` (auto-login).

2) Login  
   `POST /api/login`  
   Body:
   ```json
   { "email": "user@example.com", "password": "password123" }
   ```
   Response: 200 + cookie `laravel_session`.

3) Me  
   `GET /api/me` (butuh cookie login)  
   Response: 200 `{ success, user }` atau 401 jika belum login.

4) Logout  
   `POST /api/logout` (butuh cookie login)  
   Response: 200 `{ success, message }`.

## 3. Public Endpoints (no auth)
- List courses:  
  `GET /api/courses?search=&category=&per_page=10`
- Course detail:  
  `GET /api/courses/{id}`
- List sections:  
  `GET /api/sections?course_id=&per_page=10`
- Section detail:  
  `GET /api/sections/{id}`
- List materials:  
  `GET /api/materials?course_id=&section_id=&type=&is_preview=&per_page=10`
- Material detail:  
  `GET /api/materials/{id}`

## 4. Protected Endpoint (butuh login)
- Validate voucher:  
  `POST /api/voucher/validate`  
  Body:
  ```json
  { "voucher_code": "ABC123", "course_id": 1, "price": 100000 }
  ```
  Response: `{ success, message, voucher, discount, final_price }` atau 4xx.

## 5. Midtrans Webhook (public)
- `POST /api/midtrans/notification`  
  Body contoh (sesuaikan jika ada signature check):
  ```json
  {
    "transaction_status": "settlement",
    "order_id": "TEST-ORDER-123",
    "gross_amount": "10000.00",
    "payment_type": "bank_transfer",
    "fraud_status": "accept"
  }
  ```

## 6. Error Examples
- 401 Login gagal: `POST /api/login` dengan kredensial salah → `{ "success": false, "message": "Email atau password salah" }`
- 401 Belum login: akses `GET /api/me` atau `POST /api/voucher/validate` tanpa cookie.
- 404 Not Found: `GET /api/courses/999999` (id tidak ada/ belum publish), `GET /api/sections/{id}` atau `/api/materials/{id}` yang tidak ada.
- 405 Method Not Allowed: pakai metode selain GET pada `/api/courses`, `/api/sections`, `/api/materials`.

## 7. cURL Quick Tests
- Login:
  ```bash
  curl -i -X POST http://127.0.0.1:8000/api/login \
    -H "Accept: application/json" -H "Content-Type: application/json" \
    -d '{"email":"user@example.com","password":"password123"}' -c cookies.txt
  ```
- Me (pakai cookie):
  ```bash
  curl -i http://127.0.0.1:8000/api/me \
    -H "Accept: application/json" -b cookies.txt
  ```
- List courses:
  ```bash
  curl -i "http://127.0.0.1:8000/api/courses?per_page=5" \
    -H "Accept: application/json"
  ```
