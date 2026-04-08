# 🎬 VISUAL WORKFLOW - Frontend Development

## 🎯 THE BIG PICTURE

```
┌─────────────────────────────────────────────────────────────┐
│  YOUR PROJECT STARTUP (Copy-Paste These 2 Commands)        │
└─────────────────────────────────────────────────────────────┘

┌────────────────────────┐         ┌────────────────────────┐
│   TERMINAL 1           │         │   TERMINAL 2           │
│  (BACKEND SERVER)      │         │  (FRONTEND WATCHER)    │
├────────────────────────┤         ├────────────────────────┤
│ c:\xampp\php\php.exe   │         │ cd banjara_matrimonial│
│ artisan serve          │         │ npm run watch          │
│                        │         │                        │
│ > Starting server...   │         │ > webpack watching...  │
│ > Listening on :8000   │         │ > Ready for changes... │
│                        │         │                        │
│ 🟢 KEEP RUNNING        │         │ 🟢 KEEP RUNNING        │
└────────────────────────┘         └────────────────────────┘
         ↓                                    ↓
    POWERS                              AUTO-REBUILDS
    Routes                              Vue Components
    Database                            Tailwind CSS
                                       JavaScript
         ↖───────────────────────────────↗
                     ↓
         ┌─────────────────────────┐
         │  BROWSER                │
         ├─────────────────────────┤
         │ http://localhost:8000   │
         │                         │
         │ 💍 Banjara Matrimonial  │
         │ Navigation | Footer     │
         │ [Pages visible here]    │
         │                         │
         │ 🟢 AUTO-REFRESHES       │
         └─────────────────────────┘
                     ↑
         ┌─────────────────────────┐
         │  YOUR CODE EDITOR       │
         ├─────────────────────────┤
         │ resources/js/Pages/     │
         │ resources/css/          │
         │ routes/web.php          │
         │                         │
         │ ✏️ EDIT FILES            │
         │ 💾 SAVE (Ctrl+S)        │
         └─────────────────────────┘
```

---

## ⚡ COMPLETE DEVELOPMENT WORKFLOW

```
START
 ↓
1️⃣  TERMINAL 1: c:\xampp\php\php.exe artisan serve
    ├─ Initializes Laravel
    ├─ Creates local server
    └─ Runs on http://localhost:8000
       ↓
2️⃣  TERMINAL 2: npm run watch
    ├─ Starts webpack watcher
    ├─ Monitors all source files
    └─ Ready to rebuild
       ↓
3️⃣  BROWSER: http://localhost:8000
    ├─ Loads your site
    ├─ Shows Welcome page
    └─ 🎉 READY!
       ↓ (NOW YOU DEVELOP)
       ↓
4️⃣  EDIT FILE
    ├─ Open: resources/js/Pages/Welcome.vue
    ├─ Change text/styling
    └─ Save (Ctrl+S)
       ↓
5️⃣  AUTOMATIC PROCESS
    ├─ Terminal 2 detects change
    ├─ Rebuilds Vue component
    ├─ Compiles CSS
    └─ Updates public/ files
       ↓
6️⃣  AUTO-REFRESH
    ├─ Browser detects update
    ├─ Refreshes page
    ├─ Shows new changes
    └─ ✅ INSTANT FEEDBACK!
       ↓
7️⃣  REPEAT (Loop back to step 4️⃣)
    ├─ Edit another file
    ├─ Save
    ├─ Auto-rebuild
    └─ Auto-refresh
       ↓ (Continue until done)
DEPLOY
```

---

## 📝 STEP-BY-STEP EXECUTION

### 🟦 STEP 1: Open First Terminal

```
Windows Search → PowerShell
↓
Right-click → Run as Administrator
↓
Paste this:
┌──────────────────────────────────────────────────────────────┐
│ cd c:\xampp\htdocs\banjara_matrimonial                        │
└──────────────────────────────────────────────────────────────┘

Press Enter ↓

Paste this:
┌──────────────────────────────────────────────────────────────┐
│ c:\xampp\php\php.exe artisan serve                            │
└──────────────────────────────────────────────────────────────┘

Press Enter ↓

WAIT for:
┌──────────────────────────────────────────────────────────────┐
│ Laravel development server started                            │
│ http://127.0.0.1:8000                                        │
│                                                              │
│ [2026-04-08 15:30:00] 127.0.0.1:12345 "GET /" 200          │
└──────────────────────────────────────────────────────────────┘

✅ LEAVE THIS TERMINAL RUNNING
```

---

### 🟩 STEP 2: Open Second Terminal

```
Windows Search → PowerShell
↓
Right-click → Run as Administrator
↓
Paste this:
┌──────────────────────────────────────────────────────────────┐
│ cd c:\xampp\htdocs\banjara_matrimonial                        │
└──────────────────────────────────────────────────────────────┘

Press Enter ↓

Paste this:
┌──────────────────────────────────────────────────────────────┐
│ npm run watch                                                  │
└──────────────────────────────────────────────────────────────┘

Press Enter ↓

WAIT for:
┌──────────────────────────────────────────────────────────────┐
│ webpack is watching the files…                               │
│                                                              │
│ ● webpack █████████████████████████ done (~99%)              │
│                                                              │
│ ✔ Mix                                                        │
│   Compiled successfully in 1234ms                            │
└──────────────────────────────────────────────────────────────┘

✅ LEAVE THIS TERMINAL RUNNING
```

---

### 🟪 STEP 3: Open Browser

```
Click browser search bar
↓
Type:
┌──────────────────────────────────────────────────────────────┐
│ http://localhost:8000                                         │
└──────────────────────────────────────────────────────────────┘

Press Enter ↓

YOU WILL SEE:
┌──────────────────────────────────────────────────────────────┐
│ 💍 Banjara Matrimonial                                        │
│ [Home] [Browse] [Matches] [Messages] [Login] [Register]      │
│                                                              │
│ Find Your Perfect Match                                      │
│ [Get Started] [Browse Profiles]                              │
│                                                              │
│ Why Choose Us?                                               │
│ ✓ Verified Profiles                                          │
│ ✓ 100% Privacy & Security                                    │
│ ✓ Easy to Use Interface                                      │
│ ✓ 24/7 Customer Support                                      │
│                                                              │
│ (footer with links)                                          │
└──────────────────────────────────────────────────────────────┘

✅ YOUR SITE IS LIVE!
```

---

## 🎨 NOW LETS DEVELOP!

```
Action: EDIT A FILE

1. Open VS Code ↓
   Windows Search → "Visual Studio Code"

2. Open Folder ↓
   File → Open Folder
   → c:\xampp\htdocs\banjara_matrimonial

3. Find File ↓
   Ctrl+P → Type "Welcome.vue"
   Locates: resources/js/Pages/Welcome.vue

4. Edit ↓
   Find: <h2 class="text-4xl...">Find Your Perfect Match</h2>
   Change to: <h2 class="text-4xl...">Apna Jeevan Sathi Khojo!</h2>

5. Save ↓
   Ctrl+S

6. Terminal 2 shows ↓
   webpack is compiling...
   ✔ Compiled successfully

7. Browser auto-refreshes ↓
   Shows: Apna Jeevan Sathi Khojo!

DONE! 🎉
Repeat for more changes...
```

---

## 📂 FILE EDITING LOCATIONS

```
┌─────────────────────────────────────────────┐
│  WHAT TO EDIT                               │
├─────────────────────────────────────────────┤
│                                             │
│ 📄 Modify HOME PAGE:                        │
│   resources/js/Pages/Welcome.vue            │
│                                             │
│ 📄 Modify LOGIN PAGE:                       │
│   resources/js/Pages/Login.vue              │
│                                             │
│ 📄 Modify REGISTRATION PAGE:                │
│   resources/js/Pages/Register.vue           │
│                                             │
│ 📄 Modify BROWSE PAGE:                      │
│   resources/js/Pages/Browse.vue             │
│                                             │
│ 📄 Modify NAVIGATION BAR:                   │
│   resources/js/Components/Navigation.vue    │
│                                             │
│ 📄 Modify FOOTER:                           │
│   resources/js/Components/Footer.vue        │
│                                             │
│ 📄 Modify STYLING (Tailwind):               │
│   resources/css/app.css                     │
│                                             │
│ 📄 Modify ROUTES (URLs):                    │
│   routes/web.php                            │
│                                             │
└─────────────────────────────────────────────┘

ALL EDITS AUTO-REBUILD!
(Thanks to webpack watcher)
```

---

## 🔧 KEYBOARD SHORTCUTS WHILE DEVELOPING

```
┌──────────────────────────────────────┐
│ CODE EDITOR (VS Code)                │
├──────────────────────────────────────┤
│ Ctrl+S        → Save file            │
│ Ctrl+/        → Toggle comment       │
│ Alt+Up        → Move line up         │
│ Alt+Down      → Move line down       │
│ Ctrl+D        → Select word          │
│ Ctrl+H        → Find & Replace       │
│ Ctrl+P        → Quick file open      │
│ F2            → Rename symbol        │
└──────────────────────────────────────┘

┌──────────────────────────────────────┐
│ BROWSER                              │
├──────────────────────────────────────┤
│ F5            → Refresh page         │
│ Ctrl+Shift+R  → Hard refresh         │
│ F12           → Open DevTools        │
│ Ctrl+Shift+M  → Toggle mobile view   │
│ Tab           → Cycle through links  │
└──────────────────────────────────────┘

┌──────────────────────────────────────┐
│ WINDOWS                              │
├──────────────────────────────────────┤
│ Alt+Tab       → Switch apps          │
│ Win+V         → Paste history        │
│ Win+E         → File explorer        │
│ Super+1/2/3   → Switch windows       │
└──────────────────────────────────────┘
```

---

## 🚨 IF SOMETHING GOES WRONG

```
Problem: Website blank/not loading
├─ Check: Is Terminal 1 showing errors?
├─ Fix: Restart Terminal 1
└─ Try: Hard refresh (Ctrl+Shift+R)

Problem: Changes not showing in browser
├─ Check: Does Terminal 2 say "Compiled successfully"?
├─ Check: Did you save file (Ctrl+S)?
├─ Fix: Refresh browser (F5)
└─ Try: Clear cache (Ctrl+Shift+Delete)

Problem: Build error in Terminal 2
├─ Read: Error message (tells you which file)
├─ Fix: Open that file and fix the error
├─ Save: File with Ctrl+S
└─ Terminal 2: Will retry automatically

Problem: Port 8000 already in use
├─ Run: c:\xampp\php\php.exe artisan serve --port=8001
└─ Visit: http://localhost:8001

Problem: npm not found
├─ Fix: Install Node.js from nodejs.org
└─ Restart: PowerShell after install
```

---

## ✅ VERIFICATION CHECKLIST

```
Before starting development, verify:

Terminal 1 (artisan serve):
  ✅ Shows "127.0.0.1:8000"
  ✅ Shows "GET /" 200 requests
  ✅ No errors in red text
  ✅ Terminal is RUNNING

Terminal 2 (npm run watch):
  ✅ Shows "webpack is watching"
  ✅ Shows "Compiled successfully"
  ✅ No errors in red text
  ✅ Terminal is RUNNING

Browser:
  ✅ Shows http://localhost:8000
  ✅ Loads Banjara site
  ✅ Navigation bar visible
  ✅ No error messages
  ✅ Responsive design works

Code Editor:
  ✅ Folder opened: banjara_matrimonial
  ✅ Files visible in sidebar
  ✅ Can open files
  ✅ Can edit text
```

All ✅? You're ready to develop! 🎉

---

## 🎯 YOUR FIRST EDIT

```
Let's make your FIRST change:

1. In VS Code:
   Ctrl+P → Welcome.vue

2. Find line:
   Find Your Perfect Match

3. Change to:
   Swagat hai! Dulha/Dulhan Khojo! 💍

4. Save:
   Ctrl+S

5. Look at Terminal 2:
   Should see "Compiled successfully"

6. Check Browser:
   Should see new text!

7. Success! 🎉
   You just edited your site!

Repeat this process for anything you want to change!
```

---

## 🚀 QUICK COMMANDS REFERENCE

```powershell
# DEVELOPMENT (use these daily)
npm run watch           # Watch mode (AUTO-REBUILD)
npm run dev            # One-time dev build
npm run production     # Final optimized build

# BACKEND (Laravel)
artisan serve          # Start dev server
artisan migrate        # Run database migrations
artisan tinker         # Interactive shell
artisan cache:clear   # Clear cache

# GIT (if using version control)
git status            # Check changes
git add .             # Stage all
git commit -m "msg"   # Commit
git push              # Push to server
```

---

## 📊 FOLDER STRUCTURE (QUICK REFERENCE)

```
banjara_matrimonial/
├── resources/                    ← EDIT THESE (Frontend)
│   ├── js/
│   │   ├── Pages/               ← Page components
│   │   ├── Components/          ← Reusable components
│   │   ├── Layouts/             ← Layout components
│   │   └── app.js               ← Main JS file
│   └── css/
│       └── app.css              ← Styling
├── routes/
│   └── web.php                  ← URLs
├── app/                         ← Edit for backend
│   ├── Http/
│   ├── Models/
│   └── ...
├── database/
│   └── migrations/              ← Database schemas
├── public/                      ← DON'T EDIT (auto-generated)
│   ├── js/app.js               ← Compiled output
│   └── css/app.css             ← Compiled output
└── package.json                 ← npm dependencies
```

---

## 🎓 DEVELOPMENT TIPS

```
💡 Tip 1: Keep your code organized
   Create meaningful component names
   Keep related files together

💡 Tip 2: Use browser DevTools
   F12 → Right panel shows errors
   Helps debug quickly

💡 Tip 3: Make small changes
   Edit one thing → Save → See result
   Easier to find bugs

💡 Tip 4: Test on mobile
   F12 → Toggle device toolbar
   Ensure responsive design

💡 Tip 5: Save frequently
   Ctrl+S every few seconds
   Prevents accidental losses
```

---

## 🎉 YOU'RE READY!

```
RUN THESE 2 COMMANDS:

Terminal 1:
  cd c:\xampp\htdocs\banjara_matrimonial
  c:\xampp\php\php.exe artisan serve

Terminal 2:
  cd c:\xampp\htdocs\banjara_matrimonial
  npm run watch

Browser:
  http://localhost:8000

THEN START EDITING! 🚀
```

---

**Happy Coding! 💍✨**
