# Documentação das Rotas da API - Sistema de Registro de Ponto

## Autenticação

### POST /login

**Descrição**: Autentica o usuário e retorna o token JWT.

**Payload:**

```json
{
  "email": "usuario@exemplo.com",
  "password": "senha123"
}
```

**Resposta de sucesso:**

```json
{
  "data": {
    "token": "<jwt_token>",
    "token_type": "bearer",
    "expires_in": 3600,
    "role": true
  }
}
```

---

## Batida de Ponto

### POST /clockin

**Autenticado com JWT**

**Descrição**: Registra a batida de ponto do usuário conforme sua agenda de trabalho.

**Requisitos:**

- Usuário deve ter jornada cadastrada.
- O backend valida se o horário atual está dentro da tolerância configurada.

**Resposta de sucesso:**

```json
{
  "data": {
    "id": 3,
    "user_id": 1,
    "date": "2025-06-14",
    "morning_clock_in": "08:05:00",
    "morning_clock_out": null,
    "afternoon_clock_in": null,
    "afternoon_clock_out": null,
    ...
  },
  "message": "Clock record successfully saved/updated"
}
```

**Resposta de erro:**

```json
{
  "error": "Clock-in not completed",
  "message": "The user has not yet clocked in for today."
}
```

---

## Consultar Registro de Ponto (Hoje)

### GET /today-records

**Autenticado com JWT**

**Descrição**: Retorna o registro de ponto do usuário no dia atual.

**Resposta de sucesso:**

```json
{
  "data": [
    {
      "id": 12,
      "date": "2025-06-14",
      "morning_clock_in": "08:03:00",
      "morning_clock_out": "12:01:00",
      "afternoon_clock_in": "14:02:00",
      "afternoon_clock_out": null,
      ...
    }
  ]
}
```

**Resposta de erro:**

```json
{
  "error": "No attendance records found",
  "message": "There are no attendance records for today."
}
```

---

## Registrar novo usuário

### POST /register

**Middleware: jwt + admin**

**Payload:**

```json
{
  "name": "João Silva",
  "email": "joao@empresa.com",
  "password": "123456",
  "password_confirmation": "123456",
  "role": "user",
  "schedule_type": "regular",
  "interval": 15,
  "morning_clock_in": "08:00",
  "morning_clock_out": "12:00",
  "afternoon_clock_in": "13:00",
  "afternoon_clock_out": "17:00"
}
```

**Resposta de sucesso:**

```json
{
  "data": {
    "user": {
      "id": 5,
      "name": "João Silva",
      ...
    },
    "token": "<jwt_token>"
  },
  "message": "User successfully registered"
}
```

---

## Logout

### POST /logout

**Middleware: jwt + admin**

**Descrição**: Invalida o token JWT do usuário.

**Resposta:**

```json
{
  "message": "Successfully logged out"
}
```

---

## Atualizar usuário

### PUT /user/{id}

**Middleware: jwt + admin**

**Payload:** Mesmo formato do cadastro.

**Resposta de sucesso:**

```json
{
  "success": true,
  "message": "Usuário atualizado com sucesso",
  "data": {
    "id": 5,
    "name": "João Silva Atualizado",
    "work_schedule": {...}
  }
}
```

---

## Listar todos os usuários

### GET /user

**Middleware: jwt + admin**

**Resposta:**

```json
{
  "success": true,
  "data": [
    { "id": 1, "name": "Admin" },
    { "id": 2, "name": "João" }
  ]
}
```

---

## Gerar Relatório de Presença (PDF)

### POST /attendance-report

**Middleware: jwt + admin**

**Descrição**: Gera um relatório em PDF com os registros de todos os usuários no dia atual.

**Resposta:**

```json
{
  "data": {
    "id": 1,
    "pdf_file": "exports/attendance_report_1_1720713433.pdf"
  },
  "download_url": "http://localhost:8000/api/download-report/attendance_report_1_1720713433.pdf",
  "message": "Attendance report generated and saved successfully."
}
```

---

## Baixar Relatório

### GET /download-report/{fileName}

**Middleware: jwt + admin**

**Descrição**: Faz o download em cima da url do arquivo PDF gerado anteriormente (URL temporária).

---

## Excluir usuário

### DELETE /user/{id}

**Middleware: jwt + admin**

**Descrição**: Remove o usuário, seus registros de ponto e sua jornada.

**Resposta:**

```json
{
  "success": true,
  "message": "Usuário excluído com sucesso."
}
```

---

## Observações

- Todas as requisições protegidas devem conter o header:

```http
Authorization: Bearer <jwt_token>
```

- Datas seguem o formato `YYYY-MM-DD`.
- Horários seguem o formato `HH:mm` ou `HH:mm:ss` quando se trata de registro atual.
- O campo `interval` define a tolerância em minutos para registro de ponto.

