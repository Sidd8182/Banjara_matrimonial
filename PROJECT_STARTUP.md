# 🎉 Banjara Matrimonial - Project Startup Guide

## ✅ Setup Complete!

Your matrimonial site is now fully configured with:
- ✅ Database created: `banjara_matrimonial_db`
- ✅ All migrations run
- ✅ Frontend structure created
- ✅ All pages built and compiled

---

## 🚀 How to Start the Project

### Option 1: Using Apache (Recommended for XAMPP)

1. **Ensure Apache & MySQL are running:**
   ```
   - Open XAMPP Control Panel
   - Click "Start" for Apache
   - Click "Start" for MySQL
   ```

2. **Access the site:**
   ```
   http://localhost/banjara_matrimonial
   ```

### Option 2: Using Laravel Development Server

Open PowerShell and run:

```powershell
cd c:\xampp\htdocs\banjara_matrimonial
c:\xampp\php\php.exe artisan serve
```

Then access: `http://localhost:8000`

---

## 📡 Watch Mode (For Development)

To automatically rebuild when you make changes:

```powershell
cd c:\xampp\htdocs\banjara_matrimonial
npm run watch
```

This will:
- Watch for changes in `resources/` folder
- Automatically rebuild Vue components
- Recompile Tailwind CSS
- Refresh your browser automatically

---

## 📁 Project Structure

```
banjara_matrimonial/
├── app/
│   └── Http/
│       ├── Controllers/     (Create controllers here)
│       ├── Kernel.php      (Middleware registration)
│       └── Middleware/
│           └── HandleInertiaRequests.php
├── resources/
│   ├── js/
│   │   ├── Pages/          ✅ Page components
│   │   │   ├── Welcome.vue
│   │   │   ├── Login.vue
│   │   │   ├── Register.vue
│   │   │   ├── Browse.vue
│   │   │   ├── Dashboard.vue
│   │   │   └── Profiles.vue
│   │   ├── Layouts/        ✅ Layout components
│   │   │   └── MainLayout.vue
│   │   ├── Components/     ✅ Reusable components
│   │   │   ├── Navigation.vue
│   │   │   └── Footer.vue
│   │   └── app.js
│   └── css/
│       └── app.css         ✅ Tailwind CSS
├── routes/
│   └── web.php            ✅ Routes defined
├── database/
│   ├── migrations/        ✅ Database tables
│   └── factories/
├── public/
│   ├── js/
│   │   └── app.js         ✅ Compiled JS
│   └── css/
│       └── app.css        ✅ Compiled CSS
└── config/
    └── inertia.php

```

---

## 🔗 Available Routes

| Route | Type | Description |
|-------|------|-------------|
| `/` | GET | Home/Welcome page |
| `/login` | GET | Login page |
| `/register` | GET | Registration page |
| `/browse` | GET | Browse profiles (public) |
| `/dashboard` | GET | User dashboard (auth required) |
| `/profiles` | GET | User profiles (auth required) |

---

## 🎨 Current Pages

### 1. **Welcome Page** (`/`)
- Hero section with features
- Call-to-action buttons
- Feature highlight cards

### 2. **Register Page** (`/register`)
- Full registration form
- Name, Email, Phone, Gender, Password fields
- Terms & Conditions checkbox
- Link to login page

### 3. **Login Page** (`/login`)
- Email and password fields
- Remember me checkbox
- Forgot password link
- Social login options
- Link to registration

### 4. **Browse Profiles Page** (`/browse`)
- Advanced search filters
- Age range, Religion, Location, Caste filters
- Profile cards grid
- Like/Skip/View actions
- Sample profiles loaded

### 5. **Dashboard Page** (`/dashboard`)
- Statistics cards (members, matches, stories, ratings)
- Quick action buttons
- New matches section
- Profile cards

### 6. **Old Profiles Page** (`/profiles`)
- Legacy page - can be updated or removed

---

## 💻 Development Commands

```powershell
# Install dependencies (if needed)
npm install

# Development build (one time)
npm run dev

# Watch mode (recommended for development)
npm run watch

# Production build (optimized)
npm run production

# View Laravel logs
c:\xampp\php\php.exe artisan tinker

# Create migration
c:\xampp\php\php.exe artisan make:migration create_table_name

# Run migrations
c:\xampp\php\php.exe artisan migrate
```

---

## 🛠️ Backend Setup (Next Steps)

### 1. Create Models & Migrations
```powershell
c:\xampp\php\php.exe artisan make:model Profile --migration
c:\xampp\php\php.exe artisan make:model Match --migration
c:\xampp\php\php.exe artisan make:model Message --migration
```

### 2. Create Controllers
```powershell
c:\xampp\php\php.exe artisan make:controller ProfileController
c:\xampp\php\php.exe artisan make:controller MatchController
```

### 3. Add API Routes
Update `routes/api.php` with your API endpoints

---

## 🎨 Customization Tips

### Colors (Tailwind)
Edit `tailwind.config.js`:
- Primary color: `#dc2626` (Red)
- Secondary color: `#f97316` (Orange)

### Add New Page
1. Create component in `resources/js/Pages/MyPage.vue`
2. Add to `resources/js/app.js` pages object
3. Add route in `routes/web.php`
4. Run `npm run dev` to build

### Add Components
1. Create in `resources/js/Components/MyComponent.vue`
2. Import in your page: `import MyComponent from '@/Components/MyComponent.vue'`
3. Use in template: `<MyComponent />`

---

## 📝 Common Tasks

### Change Database Name
Edit `.env`:
```
DB_DATABASE=your_database_name
```

### Add CSS Classes
Edit `resources/css/app.css` and rebuild:
```css
@layer components {
    .my-custom-btn {
        @apply px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition;
    }
}
```

### Create API Endpoint
In `routes/api.php`:
```php
Route::get('/profiles', function () {
    return response()->json(['profiles' => []]);
});
```

---

## 🚨 Troubleshooting

### Mix build fails
```powershell
npm install --legacy-peer-deps
npm run dev
```

### Database connection error
- Check `DB_HOST`, `DB_PORT`, `DB_USERNAME` in `.env`
- Ensure MySQL is running in XAMPP
- Run `c:\xampp\php\php.exe artisan migrate`

### Pages not loading
- Run `npm run dev` to rebuild
- Clear browser cache (Ctrl+Shift+Delete)
- Check `routes/web.php` for route definition

### Port 8000 already in use
```powershell
c:\xampp\php\php.exe artisan serve --port=8001
```

---

## 📦 Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Inertia.js Documentation](https://inertiajs.com)
- [Vue 3 Documentation](https://vuejs.org)
- [Tailwind CSS Documentation](https://tailwindcss.com)

---

## ✨ Next: What to Build

1. **Authentication** - Implement user login/registration logic
2. **Database Models** - Create Profile, Match, Message models
3. **API Endpoints** - Build backend API for profile operations
4. **Search Functionality** - Implement search and filter logic
5. **Messaging System** - Add real-time messaging
6. **Admin Panel** - Build admin dashboard

---

**🎉 You're all set! Start building your matrimonial site!**
