@echo off
:: Iniciar Apache directamente en segundo plano
start "" /b "C:\xampp\apache\bin\httpd.exe"

:: Iniciar MySQL directamente en segundo plano
start "" /b "C:\xampp\mysql\bin\mysqld.exe"

:: Esperar unos segundos para asegurarse de que los servicios se inicien
timeout /t 5 >nul

:: Cambiar al directorio de tu aplicación
cd "C:\xampp\htdocs\punto_de_venta"

:: Iniciar la aplicación con npm
npm start

:: Salir del script
exit
