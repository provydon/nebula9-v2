# Zoo Management API

A Laravel-based REST API for managing zoo enclosures and animals with business rule validation.

## Features

- **Enclosure Management**: CRUD operations for enclosures with filtering capabilities
- **Animal Management**: CRUD operations for animals with business rule enforcement
- **Transfer System**: Move animals between enclosures with validation
- **Business Rules**:
  - Rule #1 (Survival): Animal's preferred environment must match enclosure type
  - Rule #2 (Space): Animals cannot be added to enclosures at maximum capacity

## Quick Start

### Prerequisites

- PHP 8.2+
- Composer
- SQLite (or MySQL/PostgreSQL)

### Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd nebula9-v2
```

2. Install dependencies:
```bash
composer install
```

3. Set up environment:
```bash
cp .env.example .env
php artisan key:generate
```

4. Run migrations:
```bash
php artisan migrate
```

5. Start the development server:
```bash
php artisan serve
```

The API will be available at `http://localhost:8000/api`

### Running Tests

```bash
php artisan test
```

## API Endpoints

### Enclosures

- `GET /api/enclosures` - List all enclosures (supports `?type=`, `?available=1`, `?full=1`)
- `GET /api/enclosures/{id}` - Show enclosure details
- `POST /api/enclosures` - Create new enclosure
- `PUT /api/enclosures/{id}` - Update enclosure
- `DELETE /api/enclosures/{id}` - Delete enclosure

### Animals

- `GET /api/animals` - List all animals (supports `?specie=`, `?preferred_environment=`, `?enclosure_id=`)
- `GET /api/animals/{id}` - Show animal details
- `POST /api/animals` - Create new animal
- `PUT /api/animals/{id}` - Update animal
- `DELETE /api/animals/{id}` - Delete animal
- `POST /api/animals/{animal}/transfer` - Transfer animal to another enclosure

### Example Requests

**Create an enclosure:**
```bash
POST /api/enclosures
{
  "name": "Savannah Enclosure",
  "type": "Savannah",
  "capacity": 10
}
```

**Add an animal:**
```bash
POST /api/animals
{
  "name": "Lion",
  "specie": "Panthera leo",
  "preferred_environment": "Savannah",
  "enclosure_id": 1
}
```

**Transfer an animal:**
```bash
POST /api/animals/1/transfer
{
  "target_enclosure_id": 2
}
```

## Architecture Decisions

### Service Layer Pattern

**Why Services?**

I chose to implement a Service layer (`EnclosureService`, `AnimalService`) to separate business logic from HTTP concerns. This decision was driven by several factors:

1. **Testability**: Services can be easily mocked and tested independently of HTTP layer
   ```php
   // Easy to test business logic without HTTP layer
   $mockService = Mockery::mock(AnimalService::class);
   $mockService->shouldReceive('transfer')->once();
   ```

2. **Reusability**: Business logic can be reused from controllers, commands, jobs, or other services
   ```php
   // Same service method can be called from anywhere
   $service->transfer($animalId, $enclosureId); // From controller
   $service->transfer($animalId, $enclosureId); // From command
   ```

3. **Separation of Concerns**: Controllers handle HTTP, Services handle business logic
   - Controllers: Request validation, HTTP responses, status codes
   - Services: Business rules, data manipulation, validation logic

4. **Maintainability**: Business rules are centralized in one place
   ```php
   // All business rules in one method
   protected function validatePlacement(int $enclosureId, string $preferredEnvironment)
   {
       // Rule #1: Environment match
       // Rule #2: Capacity check
   }
   ```

### Form Request Validation

**Why Form Requests?**

I used Form Request classes (`StoreEnclosureRequest`, `UpdateEnclosureRequest`, etc.) instead of inline validation:

1. **Separation**: Validation rules separated from controller logic
2. **Reusability**: Same validation rules can be used across different contexts
3. **Testability**: Validation logic can be tested independently
4. **Clean Controllers**: Controllers focus on orchestration, not validation details

### Eloquent Features

**Why Leverage Eloquent Directly?**

Instead of creating wrapper methods, I used Eloquent's built-in features:

1. **Query Scopes**: Reusable query constraints (`byType()`, `available()`, `full()`)
   ```php
   Enclosure::byType('Savannah')->available()->get();
   ```

2. **Accessors**: Computed properties (`current_occupancy`, `is_full`, `is_available`)
   ```php
   $enclosure->is_full; // Computed property
   ```

3. **Eager Loading**: Prevent N+1 queries with `with()` and `withCount()`
   ```php
   Enclosure::with('animals')->withCount('animals')->get();
   ```

4. **Method Chaining**: Fluent interface for readable queries
   ```php
   ->when(isset($filters['type']), fn($q) => $q->byType($filters['type']))
   ```

### Exception Handling

**Why Custom Exception Handler?**

I implemented a global exception handler in `bootstrap/app.php` to:

1. **Consistency**: All API errors return JSON with consistent format
2. **Proper Status Codes**: Different exceptions return appropriate HTTP status codes
   - `ValidationException` → 422
   - `ModelNotFoundException` → 404
   - `AuthenticationException` → 401
3. **User Experience**: Clear error messages for API consumers
4. **Security**: Hide sensitive information in production

### Edge Case Handling

**How Edge Cases Are Handled:**

1. **Full Enclosure**: Returns 422 with clear error message
   ```json
   {
     "message": "Enclosure is at maximum capacity (10)",
     "errors": {
       "enclosure_id": ["Enclosure is at maximum capacity (10)"]
     }
   }
   ```

2. **Environment Mismatch**: Returns 422 with descriptive error
   ```json
   {
     "message": "Animal's preferred environment (Aquatic) does not match enclosure type (Savannah)",
     "errors": {
       "enclosure_id": ["Animal's preferred environment (Aquatic) does not match enclosure type (Savannah)"]
     }
   }
   ```

3. **Non-existent Resources**: Returns 404
   ```json
   {
     "message": "Resource not found."
   }
   ```

4. **Validation Errors**: Returns 422 with field-specific errors
   ```json
   {
     "message": "The given data was invalid.",
     "errors": {
       "name": ["The name field is required."]
     }
   }
   ```

## Testing Strategy

### Test Coverage

- **Unit Tests**: Test service layer business logic independently
  - `tests/Unit/EnclosureServiceTest.php` - 5 tests
  - `tests/Unit/AnimalServiceTest.php` - 6 tests

- **Feature Tests**: Test API endpoints end-to-end
  - `tests/Feature/EnclosureControllerTest.php` - 6 tests
  - `tests/Feature/AnimalControllerTest.php` - 10 tests

### Test Scenarios Covered

**Happy Paths:**
- Creating enclosures and animals
- Listing with filters
- Updating resources
- Transferring animals successfully

**Failure Cases:**
- Adding animal to mismatched environment (Rule #1)
- Adding animal to full enclosure (Rule #2)
- Transferring to invalid enclosure
- Non-existent resources (404)
- Validation errors (422)

**Example Test:**
```php
test('rejects animal when environment does not match', function () {
    $enclosure = Enclosure::factory()->create(['type' => 'Savannah']);
    
    $response = $this->postJson('/api/animals', [
        'name' => 'Penguin',
        'specie' => 'Spheniscidae',
        'preferred_environment' => 'Aquatic',
        'enclosure_id' => $enclosure->id,
    ]);
    
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['enclosure_id']);
});
```

## Code Quality

### PSR-12 Compliance

- Proper type hints on all methods
- Return types specified
- Consistent code formatting
- Meaningful variable and method names

### Type Safety

```php
public function transfer(int $animalId, int $targetEnclosureId): Animal
{
    // Type hints ensure correct usage
}
```

### Clean Code Principles

- Single Responsibility: Each class has one clear purpose
- DRY (Don't Repeat Yourself): Business logic centralized in services
- SOLID Principles: Dependency injection, interface segregation

## Database Schema

The application uses two main tables:

**enclosures:**
- `id` (primary key)
- `name` (string)
- `type` (string) - e.g., "Savannah", "Forest", "Aquatic"
- `capacity` (integer)
- `created_at`, `updated_at` (timestamps)

**animals:**
- `id` (primary key)
- `name` (string)
- `specie` (string)
- `preferred_environment` (string) - must match enclosure type
- `enclosure_id` (foreign key, nullable)
- `created_at`, `updated_at` (timestamps)

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AnimalController.php
│   │   └── EnclosureController.php
│   └── Requests/
│       ├── StoreAnimalRequest.php
│       ├── UpdateAnimalRequest.php
│       ├── TransferAnimalRequest.php
│       ├── StoreEnclosureRequest.php
│       └── UpdateEnclosureRequest.php
├── Models/
│   ├── Animal.php
│   └── Enclosure.php
└── Services/
    ├── AnimalService.php
    └── EnclosureService.php

tests/
├── Feature/
│   ├── AnimalControllerTest.php
│   └── EnclosureControllerTest.php
└── Unit/
    ├── AnimalServiceTest.php
    └── EnclosureServiceTest.php
```

## Why This Architecture?

### Service Layer for Transfers

I used a Service class for transfers (`AnimalService::transfer()`) because:

1. **Business Logic Complexity**: Transfer involves multiple validations (environment match, capacity check)
2. **Reusability**: Transfer logic can be called from controllers, commands, or scheduled jobs
3. **Testability**: Easy to unit test transfer logic without HTTP layer
4. **Maintainability**: All transfer-related logic in one place

### Why Not Repository Pattern?

I chose Services over Repositories because:

1. **Simplicity**: Eloquent already provides a clean abstraction layer
2. **Laravel Conventions**: Services are more common in Laravel applications
3. **Less Abstraction**: Direct Eloquent usage is more readable and maintainable
4. **Sufficient**: For this project size, Services provide adequate separation

### Why Not Domain Models?

I kept models simple and used Services for business logic because:

1. **Laravel Convention**: Models focus on data, Services handle business logic
2. **Clarity**: Clear separation between data access and business rules
3. **Testability**: Services can be tested independently of models

## Future Improvements

- Add authentication/authorization
- Implement pagination for list endpoints
- Add API documentation (Swagger/OpenAPI)
- Add rate limiting
- Implement soft deletes
- Add event logging for transfers
- Add caching for frequently accessed data

## License

This project is for demonstration purposes.
