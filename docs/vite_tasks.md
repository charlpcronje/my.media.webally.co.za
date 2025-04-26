# Vite Migration Tasks for Media Manager Frontend

This document outlines the step-by-step process to migrate the Media Manager React frontend from Create React App (CRA) to [Vite](https://vitejs.dev/). Follow these tasks in order, checking off each one as you complete it. For each step, see Vite and plugin docs for details where needed.

---

## 1. Preparation
- [ ] **Backup your project** (optional but recommended).
- [ ] Review this checklist and ensure you have committed all recent changes.

---

## 2. Remove CRA-Specific Dependencies
- [ ] Remove `react-scripts` and any unused CRA-related packages from `package.json`.
- [ ] Remove any CRA-specific config files (e.g., `setupTests.js`, `reportWebVitals.js`, `serviceWorker.js` if not customized).

---

## 3. Install Vite and Plugins
- [ ] Install Vite and the React plugin:
  ```sh
  npm install --save-dev vite @vitejs/plugin-react
  ```
- [ ] Update `package.json` scripts:
  ```json
  "scripts": {
    "dev": "vite",
    "build": "vite build",
    "preview": "vite preview"
  }
  ```

---

## 4. Update Project Structure
- [ ] Ensure `index.html` is in the project root or `/frontend/public` (Vite will copy from `public` if present).
- [ ] Change the entry point from `src/index.js` to `src/main.jsx` (rename file if needed).
- [ ] Update `index.html` to use the Vite syntax for script loading:
  ```html
  <script type="module" src="/src/main.jsx"></script>
  ```
- [ ] Remove any `%PUBLIC_URL%` or other CRA template variables from `index.html`.

---

## 5. Tailwind CSS and PostCSS
- [ ] Ensure `tailwindcss`, `postcss`, and `autoprefixer` are installed.
- [ ] Update `postcss.config.js` if needed (Vite works with standard config).
- [ ] Update `tailwind.config.js` content paths if necessary:
  ```js
  module.exports = {
    content: ["./index.html", "./src/**/*.{js,jsx,ts,tsx}"],
    theme: { extend: {} },
    plugins: [],
  };
  ```
- [ ] Test Tailwind classes in a component to confirm working.

---

## 6. Environment Variables
- [ ] Rename any `.env` variables used in frontend to start with `VITE_` (e.g., `VITE_API_URL`).
- [ ] Update all code references from `process.env.REACT_APP_...` to `import.meta.env.VITE_...`.

---

## 7. Update Imports and Aliases
- [ ] Review all import paths. Vite supports most relative imports, but check for any CRA-specific alias usage.
- [ ] If using aliases (like `@/components`), configure them in `vite.config.js` using the `resolve.alias` option.

---

## 8. Service Worker and PWA (Optional)
- [ ] If you use a service worker or want PWA support, install and configure [`vite-plugin-pwa`](https://vite-plugin-pwa.netlify.app/).
- [ ] Migrate any custom service worker logic to the new plugin's conventions.

---

## 9. Test and Debug
- [ ] Run `npm run dev` and verify the app compiles and loads.
- [ ] Test all major features: routing, context, API calls, Tailwind styling, forms, etc.
- [ ] Check for any missing assets or import errors.

---

## 10. Clean Up
- [ ] Remove any leftover CRA or unused config files.
- [ ] Update documentation (README, this file) to reference Vite instead of CRA.

---

## 11. Production Build
- [ ] Run `npm run build` and verify the output in `dist/`.
- [ ] Optionally, run `npm run preview` to test the production build locally.

---

## 12. Deployment
- [ ] Update your deployment scripts or hosting config to serve from `dist/`.
- [ ] Test deployment in your target environment.

---

## References
- [Vite Docs](https://vitejs.dev/guide/)
- [Vite React Plugin](https://vitejs.dev/guide/features.html#jsx)
- [Vite + Tailwind](https://tailwindcss.com/docs/guides/vite)
- [Vite Plugin PWA](https://vite-plugin-pwa.netlify.app/)

---

**Tip:** Commit after each major step for easy rollback.
