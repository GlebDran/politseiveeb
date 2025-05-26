# Politsei Infosüsteem

- Admin kasutaja: test12
- Parool: test12

🛡️ Veebirakendus politsei töö haldamiseks: töötajad, kuriteod, kurjategijad, osakonnad.

## 📌 Funktsionaalsus

- ✅ Sisselogimine / registreerimine (admin ja kasutajad)
- ✅ Admini vaade:
  - Lisa ja kustuta politseinikke
  - Halda kasutajate rolle
  - Lisa ja kustuta kuritegusid
- ✅ Kasutaja vaade:
  - Vaata politseinikke ja osakondi
- ✅ Turvaline parooli salvestus (bcrypt `password_hash`)
- ✅ Andmebaasi seosed ja välisvõtmed (foreign keys)

## 🗂 Andmebaasi struktuur

- **kasutajad1**: kõik kasutajad (`roll = 1` → admin)
- **politseinik** ja **politseiosakond**: töötajad ja osakonnad
- **kuritegevus**, **kurjategija**, **kuriteo_kurjategija**: kuriteod ja seosed

![Снимок экрана 2025-05-26 134657](https://github.com/user-attachments/assets/997b6817-7a5b-4c76-aa16-496b59ce82d4)

## 🚀 Käivitamine (XAMPP)

1. Impordi `phpMyAdmin` kaudu SQL-struktuur ja andmed
2. Aseta failid kausta `htdocs/PolitseiVeeb`
3. Ava `http://localhost/PolitseiVeeb/login2.php`

## 🔗 Link https://glebdranitson24.thkit.ee/content/PolitseiVeeb/login2.php
