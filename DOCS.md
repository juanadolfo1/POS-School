# POS-School — Documentación del Proyecto

Sistema de Punto de Venta (POS) para escuelas. Gestiona alumnos, pagos de colegiaturas, becas, y control de promociones por pronto pago.

**Stack:** Laravel (PHP) + PostgreSQL + JWT Auth

---

## Módulos

### 1. Autenticación (`AuthController`)
- Login con JWT
- Registro de usuarios
- Refresh token
- Consulta de módulos por rol

### 2. Alumnos (`StudentController`)
- CRUD de alumnos
- Búsqueda por texto (full-text search con tsvector)
- Consulta por UUID (para QR)
- Consulta por grupo

### 3. Tutores (`TutorController`)
- CRUD de tutores vinculados a alumnos
- Relación padre/madre/otro

### 4. Catálogos (`CatalogController`)
- Ciclos escolares
- Niveles académicos
- Grupos
- Conceptos de pago (colegiaturas y servicios)

### 5. Pagos (`PaymentController`)
- Consulta de pagos por alumno
- Pagos pendientes con validación de descuento
- Pagos de servicios por nivel académico
- Registro de pagos (checkout)
- **Módulo de Pronto Pago** — valida automáticamente si el alumno es elegible para descuento

### 6. Documentos (`DocumentController`)
- Generación de ticket por folio
- Corte de caja por día
- Reporte de pagos pendientes

### 7. QR (`QrController`)
- Generación de código QR para identificación de alumnos

---

## Diagrama de Relaciones (ER)

```
users
├── role_id → roles

roles
└── role_operations → operations → modules

session_tokens
└── user_id → users

people (personas genéricas: alumnos y tutores)

students
├── person_id → people
└── search_text (tsvector para búsqueda)

student_tutors
├── student_id → students
└── person_id → people

scholar_years (ciclos escolares)

cat_academic_levels (preescolar, primaria, secundaria)

groups
├── scholar_year_id → scholar_years
└── academic_level_id → cat_academic_levels

student_groups
├── group_id → groups
└── student_id → students

scholarships (becas)
├── student_id → students
└── scholar_year_id → scholar_years

cat_pay_concepts (conceptos de pago: colegiaturas/servicios)
├── scholar_year_id → scholar_years
└── id_cat_academic_level → cat_academic_levels

cat_payment_methods (efectivo, transferencia, tarjeta)

cat_folios (folios consecutivos para tickets)
└── scholar_year_id → scholar_years

tickets
├── payment_method_id → cat_payment_methods
└── student_group_id → student_groups

ticket_products
├── ticket_id → tickets
└── pay_concept_id → cat_pay_concepts

payments
└── ticket_product_id → ticket_products

ticket_scholarships
├── scholarship_id → scholarships
└── ticket_id → tickets
```

---

## Tablas — Detalle

### `users`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| name | string | Nombre del usuario |
| email | string | Correo (login) |
| password | string | Contraseña hasheada |
| role_id | FK → roles | Rol asignado |

### `roles`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| role_name | string(64) | Nombre del rol (Administrador, Cajero) |

### `modules`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| module_name | string(64) | Nombre del módulo (Alumnos, Pagos, etc.) |
| path | string(128) | Ruta del frontend |
| icon | string(64) | Ícono |
| order | int | Orden de aparición |
| status | int | Activo/Inactivo |

### `operations`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| operation_name | string(64) | Nombre (Ver, Crear, Cobrar) |
| module_id | FK → modules | Módulo al que pertenece |

### `role_operations`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| role_id | FK → roles | Rol |
| operation_id | FK → operations | Operación permitida |

### `session_tokens`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| token | string(256) | JWT token activo |
| expiration | int | Tiempo de expiración |
| user_id | FK → users | Usuario dueño del token |

### `people`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| name | string(128) | Nombre |
| first_lastname | string(64) | Apellido paterno |
| second_lastname | string(64) | Apellido materno |
| email | string(128) | Correo (nullable) |
| status | int | Activo/Inactivo |

### `students`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| gender | char(1) | M/F |
| birthday | date | Fecha de nacimiento |
| curp | string(18) | CURP |
| uuid | string(36) | Identificador único (para QR) |
| person_id | FK → people | Datos personales |
| search_text | tsvector | Índice de búsqueda |

### `student_tutors`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| status | int | Activo/Inactivo |
| tutor_type | char(1) | P=Padre, M=Madre, O=Otro |
| relation | string(32) | Relación (Padre, Madre, Tío, etc.) |
| student_id | FK → students | Alumno |
| person_id | FK → people | Datos del tutor |

### `scholar_years`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| year | string(12) | Etiqueta (2025-2026) |
| starts_at | date | Inicio del ciclo |
| ends_at | date | Fin del ciclo |
| status | int | Activo/Inactivo |

### `cat_academic_levels`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| label | string(128) | Nombre (Preescolar, Primaria, Secundaria) |
| status | int | Activo/Inactivo |

### `groups`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| label | string(6) | Nombre del grupo (4A, 4B) |
| scholar_year_id | FK → scholar_years | Ciclo escolar |
| academic_level_id | FK → cat_academic_levels | Nivel académico |
| status | int | Activo/Inactivo |

### `student_groups`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| group_id | FK → groups | Grupo asignado |
| student_id | FK → students | Alumno |
| status | int | Activo/Inactivo |

### `scholarships`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| name | string(128) | Nombre de la beca |
| amount | float | Porcentaje o monto de descuento |
| student_id | FK → students | Alumno beneficiado |
| scholar_year_id | FK → scholar_years | Ciclo escolar |
| status | int | Activo/Inactivo |

### `cat_pay_concepts`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| label | string(128) | Nombre (Colegiatura Agosto, Inscripción) |
| amount | decimal(10,2) | Precio real |
| discount_amount | decimal(10,2) | Precio con descuento |
| last_day_with_discount | date | Fecha límite para descuento |
| pay_concept_type | enum | `tuition` (colegiatura) o `service` (servicio) |
| scholar_year_id | FK → scholar_years | Ciclo escolar |
| id_cat_academic_level | FK → cat_academic_levels | Nivel académico |
| status | int | Activo/Inactivo |

### `cat_payment_methods`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| name | string | Nombre (Efectivo, Transferencia, Tarjeta) |
| key | string(2) | Clave corta (EF, TR, TJ) |

### `cat_folios`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| key | string(2) | Prefijo (TK) |
| counter | int | Consecutivo actual |
| scholar_year_id | FK → scholar_years | Ciclo escolar |

### `tickets`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| is_full_payed | boolean | ¿Pagado completo? |
| amount | decimal(10,2) | Monto total cobrado |
| has_discount | boolean | ¿Tiene descuento? |
| discount_type | enum | `early_payment` o `scholarship` |
| discount_amount | decimal(10,2) | Monto del descuento |
| folio_ticket | string(14) | Folio único (ej: TKEF252600001) |
| payment_method_id | FK → cat_payment_methods | Método de pago |
| student_group_id | FK → student_groups | Alumno+Grupo |

### `ticket_products`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| quantity | int | Cantidad |
| discount | float | Descuento aplicado |
| total | float | Total del producto |
| ticket_id | FK → tickets | Ticket padre |
| pay_concept_id | FK → cat_pay_concepts | Concepto de pago |

### `payments`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| is_full_payment | int | 1=Liquidado, 0=Abono |
| paid_amount | decimal(10,2) | Monto pagado |
| paid_at | date | Fecha del pago |
| applied_discount | boolean | ¿Se aplicó descuento de pronto pago? |
| ticket_product_id | FK → ticket_products | Producto del ticket |

### `ticket_scholarships`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| scholarship_id | FK → scholarships | Beca aplicada |
| ticket_id | FK → tickets | Ticket donde se aplicó |

---

## Módulo de Pronto Pago (Promoción Automática)

**Ubicación:** `app/Services/PromotionService.php`

### Lógica
- No requiere tabla adicional ni inscripción manual
- Al consultar pagos pendientes, el sistema revisa **automáticamente** el historial
- Si **todos** los meses vencidos fueron pagados antes de `last_day_with_discount` → `promotion_eligible: true`
- Si **alguno** fue pagado tarde → `promotion_eligible: false` (precio real)

### Días hábiles
- Si `last_day_with_discount` cae en **sábado** → se recorre al viernes
- Si cae en **domingo** → se recorre al viernes

### Ejemplo
```
Colegiatura Agosto: límite 10/ago, precio real $2,500, con descuento $2,200

Juan pagó el 5/ago  → ✅ A tiempo → descuento aplicado
Pedro pagó el 15/ago → ❌ Tarde → precio real

En septiembre:
- Juan: promotion_eligible = true (puede seguir con descuento)
- Pedro: promotion_eligible = false (perdió la promoción)
```

### Endpoint
```
GET /api/payments/promotion/check?student_id=1&scholar_year_id=1&academic_level_id=2
```
Respuesta:
```json
{
  "status": "success",
  "data": { "eligible": true }
}
```

---

## Credenciales de prueba

| Usuario | Email | Password | Rol |
|---------|-------|----------|-----|
| Admin | admin@school.com | password123 | Administrador |
| Cajero | cajero@school.com | password123 | Cajero |

## Comandos útiles

```bash
# Iniciar PostgreSQL (Docker)
docker start postgres17

# Migrar y poblar BD
php artisan migrate:fresh --seed

# Levantar servidor
php artisan serve
```
