# 🎯 QUICK START - Copy & Paste

## ⚡ FASTEST WAY TO START (2 Steps)

### TERMINAL 1 - Copy & Paste This:
```powershell
cd c:\xampp\htdocs\banjara_matrimonial && c:\xampp\php\php.exe artisan serve
```

**Expected Output:**
```
Laravel development server started: http://127.0.0.1:8000
```

---

### TERMINAL 2 - Copy & Paste This:
```powershell
cd c:\xampp\htdocs\banjara_matrimonial && npm run watch
```

**Expected Output:**
```
webpack is watching the files...
```

---

### OPEN BROWSER:
```
http://localhost:8000
```

**You should see your matrimonial site!** 🎉

---

## 🖥️ Screenshots of What You'll See:

### Terminal 1 (artisan serve):
```
   Laravel development server started: http://127.0.0.1:8000
   [2026-04-08 12:34:56] 127.0.0.1:12345 "GET / HTTP/1.1" 200

✅ Server is running
```

### Terminal 2 (npm run watch):
```
webpack is watching the files…

● webpack █████████████████████████ done (99%) plugins

✔ Mix
  Compiled successfully in 1234ms

✅ Ready for changes
```

### Browser:
```
🎉 You see the website!

URL: http://localhost:8000
Shows: Welcome page with navigation
```

---

## 📝 Now Edit Code & Develop!

### Example: Change Welcome Page Title

**File to Edit:**
```
c:\xampp\htdocs\banjara_matrimonial\resources\js\Pages\Welcome.vue
```

**Find this line:**
```vue
<h2 class="text-4xl font-bold text-gray-900 mb-4">
  Find Your Perfect Match
</h2>
```

**Change to:**
```vue
<h2 class="text-4xl font-bold text-gray-900 mb-4">
  Apna Soulmate Khojo! 💍
</h2>
```

**Save** (Ctrl+S)

**Terminal 2 will show:**
```
webpack is compiling...
✔ Compiled successfully
```

**Browser auto-refreshes** and you see the change! ✅

---

## 🔧 Common Development Commands

### Build Files (One-time):
```powershell
npm run dev
```

### Watch Mode (Auto-rebuild) - USE THIS!:
```powershell
npm run watch
```

### Production Build (Optimized):
```powershell
npm run production
```

### Clear Laravel Cache:
```powershell
c:\xampp\php\php.exe artisan cache:clear
```

---

## 📂 Important Files to Edit:

```
Welcome Page:     resources/js/Pages/Welcome.vue
Login Page:       resources/js/Pages/Login.vue
Register Page:    resources/js/Pages/Register.vue
Browse Page:      resources/js/Pages/Browse.vue
Dashboard Page:   resources/js/Pages/Dashboard.vue

Navigation:       resources/js/Components/Navigation.vue
Footer:           resources/js/Components/Footer.vue

Styling:          resources/css/app.css
Routes:           routes/web.php
```

---

## 🎨 Quick Styling Guide (Copy-Paste)

### Button:
```html
<button class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
  Click Me
</button>
```

### Input Field:
```html
<input type="text" placeholder="Enter name" class="w-full px-3 py-2 border border-gray-300 rounded-lg" />
```

### Card:
```html
<div class="bg-white p-6 rounded-lg shadow-lg">
  Content here
</div>
```

### Grid (3 columns):
```html
<div class="grid grid-cols-3 gap-6">
  <div>Column 1</div>
  <div>Column 2</div>
  <div>Column 3</div>
</div>
```

### Responsive Grid:
```html
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
  Items here
</div>
<!-- 1 column on mobile, 2 on tablet, 3 on desktop -->
```

---

## 🚀 WORKFLOW:

```
1. Terminal 1: c:\xampp\php\php.exe artisan serve
   ↓
2. Terminal 2: npm run watch
   ↓
3. Browser: http://localhost:8000
   ↓
4. Edit files in resources/ folder
   ↓
5. Save (Ctrl+S) → Terminal 2 rebuilds → Browser refreshes
   ↓
6. Repeat step 4-5 for development
```

---

## ⚠️ IF SOMETHING DOESN'T WORK:

### Website not loading?
```
✅ Check: Is Terminal 1 running? (artisan serve)
✅ Try: http://localhost:8000 (not localhost alone)
✅ Clear cache: Ctrl+Shift+Delete in browser
```

### Changes not showing?
```
✅ Check: Is Terminal 2 running? (npm run watch)
✅ Try: Refresh browser (F5)
✅ Check: Did Terminal 2 say "Compiled successfully"?
```

### Build error in Terminal 2?
```
✅ Check: The error message (usually syntax error)
✅ Fix: The file mentioned in error
✅ Save: Ctrl+S
✅ Terminal 2 will try again
```

### Port 8000 taken?
```powershell
✅ Use: c:\xampp\php\php.exe artisan serve --port=8001
✅ Visit: http://localhost:8001
```

---

## 📊 Routes Available:

| URL | Page | Status |
|-----|------|--------|
| http://localhost:8000 | Welcome | ✅ |
| http://localhost:8000/login | Login | ✅ |
| http://localhost:8000/register | Register | ✅ |
| http://localhost:8000/browse | Browse Profiles | ✅ |
| http://localhost:8000/dashboard | Dashboard | ✅ |
| http://localhost:8000/profiles | Profiles | ✅ |

---

## 💡 TIPS FOR FASTER DEVELOPMENT:

1. **Use Two Monitors** (if available)
   - One for Code
   - One for Browser

2. **Keep Browser DevTools Open** (F12)
   - Console tab for errors
   - Elements tab for HTML structure

3. **Use Keyboard Shortcuts**
   - Alt+Tab: Switch windows
   - F5: Refresh browser
   - Ctrl+S: Save file

4. **Test Your Changes**
   - Test every edit
   - Check mobile view (F12 → Toggle device)

---

## ✨ YOU'RE GOOD TO GO!

Start with these 2 commands and begin developing:

```
Terminal 1: c:\xampp\php\php.exe artisan serve
Terminal 2: npm run watch
Browser:    http://localhost:8000
```

Edit → Save → See Changes! 🎉

**Happy Coding!** 💍
