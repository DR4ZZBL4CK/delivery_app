# Sistema de Delivery - API REST Laravel 10

## Descripción
Sistema completo de gestión de pedidos y entregas desarrollado con Laravel 10, incluyendo API REST con autenticación Sanctum, frontend integrado y documentación Swagger.

## Características Implementadas

### ✅ Backend (API REST)
- **Autenticación**: Laravel Sanctum con tokens
- **Modelos**: Camionero, Camion, Paquete, DetallePaquete, EstadoPaquete, TipoMercancia
- **CRUD Completo**: Endpoints protegidos para todas las entidades
- **Validación**: Form Requests con reglas de negocio
- **API Resources**: Respuestas consistentes con paginación
- **Seeders**: Datos de prueba consistentes
- **Tests**: PHPUnit con cobertura >=70%
- **Documentación**: Swagger/OpenAPI interactiva

### ✅ Frontend
- **Dashboard**: Panel de control con datos en tiempo real
- **Integración API**: Consumo de endpoints con JavaScript
- **UI Responsiva**: Interfaz moderna y funcional
- **Autenticación**: Sistema de login/registro

## Instalación y Configuración

### 1. Instalar dependencias
```bash
composer install
npm install
```

### 2. Configurar entorno
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Configurar base de datos
Editar `.env` con los datos de tu base de datos:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=delivery_app
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Ejecutar migraciones y seeders
```bash
php artisan migrate
php artisan db:seed
```

### 5. Iniciar servidor
```bash
php artisan serve
```

## Endpoints de la API

### Autenticación
- `POST /api/auth/register` - Registrar usuario
- `POST /api/auth/login` - Iniciar sesión
- `POST /api/auth/logout` - Cerrar sesión (requiere token)
- `GET /api/auth/me` - Obtener perfil (requiere token)

### Paquetes
- `GET /api/paquetes` - Listar paquetes (requiere token)
- `POST /api/paquetes` - Crear paquete (requiere token)
- `GET /api/paquetes/{id}` - Ver paquete (requiere token)
- `PUT /api/paquetes/{id}` - Actualizar paquete (requiere token)
- `DELETE /api/paquetes/{id}` - Eliminar paquete (requiere token)

### Camioneros
- `GET /api/camioneros` - Listar camioneros (requiere token)
- `POST /api/camioneros` - Crear camionero (requiere token)
- `GET /api/camioneros/{id}` - Ver camionero (requiere token)
- `PUT /api/camioneros/{id}` - Actualizar camionero (requiere token)
- `DELETE /api/camioneros/{id}` - Eliminar camionero (requiere token)

### Camiones
- `GET /api/camiones` - Listar camiones (requiere token)
- `POST /api/camiones` - Crear camión (requiere token)
- `GET /api/camiones/{id}` - Ver camión (requiere token)
- `PUT /api/camiones/{id}` - Actualizar camión (requiere token)
- `DELETE /api/camiones/{id}` - Eliminar camión (requiere token)

### Utilidades
- `GET /api/health-check` - Estado del API

## Documentación

### Swagger/OpenAPI
Accede a la documentación interactiva en:
```
http://localhost:8000/api/documentation
```

### Health Check
Verifica el estado del API:
```
http://localhost:8000/api/health-check
```

## Estructura del Proyecto

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/           # Controladores de la API
│   ├── Requests/          # Form Requests de validación
│   └── Resources/         # API Resources
├── Models/                # Modelos Eloquent
database/
├── factories/             # Factories para testing
├── migrations/            # Migraciones de BD
└── seeders/              # Seeders de datos
tests/
└── Feature/Api/          # Tests de la API
```

## Testing

### Ejecutar tests
```bash
php artisan test
```

### Tests específicos
```bash
php artisan test --filter=AuthTest
php artisan test --filter=PaqueteTest
php artisan test --filter=CamioneroTest
```

## Uso de la API

### 1. Registrar usuario
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "nombre": "Juan",
    "apellido": "Pérez",
    "email": "juan@test.com",
    "password": "password123"
  }'
```

### 2. Iniciar sesión
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "juan@test.com",
    "password": "password123"
  }'
```

### 3. Usar token para endpoints protegidos
```bash
curl -X GET http://localhost:8000/api/paquetes \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

## Características Técnicas

- **Laravel 10**: Framework PHP moderno
- **Sanctum**: Autenticación por tokens
- **API Resources**: Respuestas JSON consistentes
- **Form Requests**: Validación robusta
- **Paginación**: Manejo eficiente de grandes datasets
- **Tests**: Cobertura de código >=70%
- **Swagger**: Documentación automática
- **Seeders**: Datos de prueba realistas

## Contribución

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.
