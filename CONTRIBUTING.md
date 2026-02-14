# Contributing to Laravel School Management System

Thank you for considering contributing to the Laravel School Management System! This document provides guidelines and instructions for contributing to the project.

## Table of Contents

- [Code of Conduct](#code-of-conduct)
- [How Can I Contribute?](#how-can-i-contribute)
- [Development Setup](#development-setup)
- [Coding Standards](#coding-standards)
- [Pull Request Process](#pull-request-process)
- [Reporting Bugs](#reporting-bugs)
- [Suggesting Features](#suggesting-features)

## Code of Conduct

This project and everyone participating in it is governed by our commitment to creating a welcoming and inclusive environment. Please be respectful and professional in all interactions.

### Our Standards

- Use welcoming and inclusive language
- Be respectful of differing viewpoints and experiences
- Gracefully accept constructive criticism
- Focus on what is best for the community
- Show empathy towards other community members

## How Can I Contribute?

### Types of Contributions

We welcome various types of contributions:

1. **Bug Reports** - Help us identify and fix issues
2. **Feature Suggestions** - Propose new features or improvements
3. **Code Contributions** - Submit bug fixes or new features
4. **Documentation** - Improve or add to documentation
5. **Testing** - Write tests or test new features
6. **UI/UX Improvements** - Enhance the user interface and experience

## Development Setup

### Prerequisites

- PHP 8.1 or higher
- Composer
- Node.js 16+ and NPM
- MySQL/PostgreSQL/SQLite
- Git

### Setting Up Your Development Environment

1. **Fork the Repository**
   ```bash
   # Click the 'Fork' button on GitHub
   # Then clone your fork
   git clone https://github.com/YOUR-USERNAME/ramaschoollaravel.git
   cd ramaschoollaravel
   ```

2. **Add Upstream Remote**
   ```bash
   git remote add upstream https://github.com/raparty/ramaschoollaravel.git
   ```

3. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

4. **Configure Environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Set Up Database**
   ```bash
   # Configure database in .env
   php artisan migrate
   php artisan db:seed
   ```

6. **Run Development Server**
   ```bash
   php artisan serve
   ```

### Keeping Your Fork Updated

```bash
# Fetch upstream changes
git fetch upstream

# Merge upstream changes into your local main branch
git checkout main
git merge upstream/main

# Push updates to your fork
git push origin main
```

## Coding Standards

### PHP Coding Standards

This project follows **PSR-12** coding standards.

#### Key Guidelines

1. **Type Hints**
   ```php
   // Always use type hints for parameters and return types
   public function store(StoreAdmissionRequest $request): RedirectResponse
   {
       // ...
   }
   ```

2. **PHPDoc Comments**
   ```php
   /**
    * Store a newly created student admission.
    *
    * @param StoreAdmissionRequest $request
    * @return RedirectResponse
    */
   public function store(StoreAdmissionRequest $request): RedirectResponse
   {
       // ...
   }
   ```

3. **Naming Conventions**
   - Classes: `PascalCase` (e.g., `AdmissionController`)
   - Methods: `camelCase` (e.g., `storeAdmission`)
   - Variables: `camelCase` (e.g., `$studentData`)
   - Constants: `UPPER_SNAKE_CASE` (e.g., `MAX_FILE_SIZE`)
   - Database tables: `snake_case` plural (e.g., `admissions`)

4. **Eloquent Models**
   ```php
   // Use singular names
   class Admission extends Model
   {
       // Specify fillable fields
       protected $fillable = ['reg_no', 'student_name', ...];
       
       // Define relationships
       public function class(): BelongsTo
       {
           return $this->belongsTo(ClassModel::class);
       }
   }
   ```

5. **Controllers**
   ```php
   // Keep controllers thin, use services for business logic
   class AdmissionController extends Controller
   {
       public function store(StoreAdmissionRequest $request): RedirectResponse
       {
           DB::beginTransaction();
           try {
               $admission = Admission::create($request->validated());
               DB::commit();
               return redirect()->route('admissions.index')
                   ->with('success', 'Student admitted successfully');
           } catch (\Exception $e) {
               DB::rollBack();
               return back()->with('error', 'Failed to admit student');
           }
       }
   }
   ```

### Code Style Checking

We use Laravel Pint for code styling:

```bash
# Check code style
./vendor/bin/pint --test

# Automatically fix code style
./vendor/bin/pint
```

### Frontend Standards

1. **Blade Templates**
   - Use Blade components for reusable UI elements
   - Escape output with `{{ }}` (automatic)
   - Use `@csrf` directive in all forms
   - Keep templates clean and readable

2. **CSS/SCSS**
   - Follow BEM methodology where applicable
   - Use Bootstrap 5 utility classes
   - Maintain consistent spacing and naming

3. **JavaScript**
   - Use ES6+ syntax
   - Keep JavaScript minimal and progressive enhancement focused
   - Comment complex logic

## Pull Request Process

### Before Submitting

1. **Create a Feature Branch**
   ```bash
   git checkout -b feature/your-feature-name
   # or
   git checkout -b fix/bug-description
   ```

2. **Make Your Changes**
   - Follow coding standards
   - Write clear, concise commit messages
   - Add tests if applicable
   - Update documentation

3. **Test Your Changes**
   ```bash
   # Run tests
   php artisan test
   
   # Check code style
   ./vendor/bin/pint --test
   
   # Test manually in browser
   ```

4. **Commit Your Changes**
   ```bash
   # Use descriptive commit messages
   git add .
   git commit -m "Add feature: Student photo upload validation"
   ```

### Commit Message Guidelines

Format: `<type>: <subject>`

Types:
- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style changes (formatting, etc.)
- `refactor`: Code refactoring
- `test`: Adding or updating tests
- `chore`: Maintenance tasks

Examples:
```
feat: Add transport fee collection module
fix: Resolve issue with fee receipt generation
docs: Update installation instructions
style: Format AdmissionController per PSR-12
refactor: Extract fee calculation to service class
test: Add tests for exam mark entry
chore: Update dependencies
```

### Submitting the Pull Request

1. **Push to Your Fork**
   ```bash
   git push origin feature/your-feature-name
   ```

2. **Create Pull Request**
   - Go to the original repository on GitHub
   - Click "New Pull Request"
   - Select your fork and branch
   - Fill in the PR template with:
     - Clear description of changes
     - Related issue numbers (if any)
     - Screenshots (for UI changes)
     - Testing steps

3. **PR Template**
   ```markdown
   ## Description
   Brief description of changes
   
   ## Type of Change
   - [ ] Bug fix
   - [ ] New feature
   - [ ] Documentation update
   - [ ] Code refactoring
   
   ## Related Issues
   Fixes #123
   
   ## Testing
   - [ ] Tests pass locally
   - [ ] Manual testing completed
   - [ ] Code style checks pass
   
   ## Screenshots (if applicable)
   Add screenshots here
   
   ## Checklist
   - [ ] My code follows the project's coding standards
   - [ ] I have commented my code where necessary
   - [ ] I have updated the documentation
   - [ ] My changes generate no new warnings
   - [ ] I have added tests that prove my fix/feature works
   ```

### Review Process

1. Maintainers will review your PR
2. Address any requested changes
3. Once approved, your PR will be merged
4. Your contribution will be acknowledged

## Reporting Bugs

### Before Submitting a Bug Report

- Check if the issue has already been reported
- Verify it's reproducible in the latest version
- Gather relevant information (error messages, logs, etc.)

### How to Submit a Bug Report

1. Go to [Issues](https://github.com/raparty/ramaschoollaravel/issues)
2. Click "New Issue"
3. Choose "Bug Report" template
4. Provide the following information:

```markdown
**Bug Description**
Clear and concise description of the bug

**To Reproduce**
Steps to reproduce the behavior:
1. Go to '...'
2. Click on '...'
3. Scroll down to '...'
4. See error

**Expected Behavior**
What you expected to happen

**Screenshots**
If applicable, add screenshots

**Environment**
- OS: [e.g., Ubuntu 22.04]
- PHP Version: [e.g., 8.1.2]
- Laravel Version: [e.g., 10.x]
- Database: [e.g., MySQL 8.0]
- Browser: [e.g., Chrome 120]

**Additional Context**
Any other relevant information
```

## Suggesting Features

### Before Suggesting a Feature

- Check if the feature has already been suggested
- Verify it fits the project's scope and goals
- Consider if it benefits the majority of users

### How to Suggest a Feature

1. Go to [Issues](https://github.com/raparty/ramaschoollaravel/issues)
2. Click "New Issue"
3. Choose "Feature Request" template
4. Provide the following information:

```markdown
**Feature Description**
Clear description of the feature

**Problem It Solves**
What problem does this feature address?

**Proposed Solution**
How would you implement this feature?

**Alternatives Considered**
What alternatives have you considered?

**Additional Context**
Mockups, examples, or other relevant information
```

## Documentation

### Improving Documentation

Documentation contributions are highly valued:

1. **README.md** - Main project documentation
2. **docs/** - Detailed guides and references
3. **Code Comments** - PHPDoc and inline comments
4. **Wiki** - Community-contributed guides

### Documentation Guidelines

- Use clear, concise language
- Include code examples where applicable
- Keep documentation up-to-date with code changes
- Use proper Markdown formatting

## Testing

### Writing Tests

We use PHPUnit for testing:

```php
// tests/Feature/AdmissionTest.php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admission;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdmissionTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_can_create_admission(): void
    {
        $data = [
            'reg_no' => 'STU001',
            'student_name' => 'John Doe',
            // ... other fields
        ];
        
        $response = $this->post(route('admissions.store'), $data);
        
        $response->assertRedirect(route('admissions.index'));
        $this->assertDatabaseHas('admissions', ['reg_no' => 'STU001']);
    }
}
```

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter AdmissionTest

# Run with coverage
php artisan test --coverage
```

## Questions?

If you have questions about contributing:

- Open an issue with the "question" label
- Check existing discussions
- Review the documentation in `/docs`

## Recognition

Contributors will be:
- Listed in the project's contributors
- Acknowledged in release notes
- Appreciated for their valuable contributions!

Thank you for contributing to make this project better! 🎉
