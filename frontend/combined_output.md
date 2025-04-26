# Combined Markdown Export

Generated: 2025-04-26T00:07:11.891992


## Index

- `package.json` — ~101 tokens
- `postcss.config.js` — ~14 tokens
- `public\index.html` — ~71 tokens
- `public\manifest.json` — ~39 tokens
- `public\service-worker.js` — ~45 tokens
- `src\App.js` — ~194 tokens
- `src\components\admin\AnalyticsDisplay.js` — ~114 tokens
- `src\components\admin\Dashboard.js` — ~109 tokens
- `src\components\admin\MediaForm.js` — ~280 tokens
- `src\components\admin\MediaTable.js` — ~125 tokens
- `src\components\admin\UserTable.js` — ~125 tokens
- `src\components\auth\LoginForm.js` — ~151 tokens
- `src\components\auth\RegisterForm.js` — ~215 tokens
- `src\components\common\Button.js` — ~45 tokens
- `src\components\common\Card.js` — ~35 tokens
- `src\components\common\Input.js` — ~43 tokens
- `src\components\common\Loader.js` — ~67 tokens
- `src\components\common\NotFound.js` — ~57 tokens
- `src\components\layout\Footer.js` — ~35 tokens
- `src\components\layout\Header.js` — ~53 tokens
- `src\components\layout\Layout.js` — ~63 tokens
- `src\components\layout\Sidebar.js` — ~51 tokens
- `src\components\media\MediaList.js` — ~91 tokens
- `src\components\media\MediaPlayer.js` — ~110 tokens
- `src\components\media\Rating.js` — ~83 tokens
- `src\context\AdminContext.js` — ~129 tokens
- `src\context\AnalyticsContext.js` — ~99 tokens
- `src\context\AuthContext.js` — ~144 tokens
- `src\context\MediaContext.js` — ~118 tokens
- `src\context\RatingContext.js` — ~131 tokens
- `src\index.js` — ~31 tokens
- `src\input.css` — ~7 tokens
- `src\pages\Home.js` — ~81 tokens
- `src\pages\Login.js` — ~118 tokens
- `src\pages\MediaPlayer.js` — ~109 tokens
- `src\pages\admin\Analytics.js` — ~91 tokens
- `src\pages\admin\Dashboard.js` — ~89 tokens
- `src\pages\admin\MediaManagement.js` — ~192 tokens
- `src\pages\admin\UserManagement.js` — ~152 tokens
- `src\services\admin.js` — ~190 tokens
- `src\services\analytics.js` — ~159 tokens
- `src\services\api.js` — ~27 tokens
- `src\services\auth.js` — ~111 tokens
- `src\services\media.js` — ~199 tokens
- `src\services\rating.js` — ~90 tokens
- `tailwind.config.js` — ~22 tokens

**Total tokens: ~4605**

---

### `package.json`

```json
{
  "name": "media-manager-frontend",
  "version": "1.0.0",
  "private": true,
  "dependencies": {
    "@shadcn/ui": "^0.0.4",
    "autoprefixer": "^10.4.21",
    "axios": "^1.8.4",
    "postcss": "^8.5.3",
    "react": "^18.2.0",
    "react-dom": "^18.2.0",
    "react-player": "^2.16.0",
    "react-router-dom": "^7.5.1",
    "react-scripts": "^5.0.1"
  },
  "devDependencies": {
    "tailwindcss": "^4.1.4"
  },
  "scripts": {
    "start": "react-scripts start",
    "build": "react-scripts build",
    "test": "react-scripts test",
    "eject": "react-scripts eject"
  },
  "browserslist": {
    "production": [
      ">0.2%",
      "not dead",
      "not op_mini all"
    ],
    "development": [
      "last 1 chrome version",
      "last 1 firefox version",
      "last 1 safari version"
    ]
  }
}
```

### `postcss.config.js`

```js
module.exports = {
  plugins: {
    '@tailwindcss/postcss': {},
    autoprefixer: {},
  },
};
```

### `public\index.html`

```html
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="theme-color" content="#111827" />
    <meta name="description" content="Media Manager PWA - Stream and manage your media content" />
    <link rel="manifest" href="/manifest.json" />
    <title>Media Manager PWA</title>
  </head>
  <body class="bg-gray-900 text-white">
    <noscript>You need to enable JavaScript to run this app.</noscript>
    <div id="root"></div>
  </body>
</html>
```

### `public\manifest.json`

```json
{
  "short_name": "MediaMgr",
  "name": "Media Manager PWA",
  "icons": [
    {
      "src": "favicon.ico",
      "sizes": "64x64 32x32 24x24 16x16",
      "type": "image/x-icon"
    }
  ],
  "start_url": ".",
  "display": "standalone",
  "theme_color": "#111827",
  "background_color": "#111827"
}
```

### `public\service-worker.js`

```js
// Simple service worker for offline support
self.addEventListener('install', event => {
  self.skipWaiting();
});
self.addEventListener('activate', event => {
  event.waitUntil(self.clients.claim());
});
self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request).then(response => {
      return response || fetch(event.request);
    })
  );
});
```

### `src\App.js`

```js
import React from "react";
import { BrowserRouter as Router, Routes, Route } from "react-router-dom";
import Layout from "./components/layout/Layout";
import Home from "./pages/Home";
import MediaPlayerPage from "./pages/MediaPlayer";
import LoginPage from "./pages/Login";
import DashboardPage from "./pages/admin/Dashboard";
import MediaManagementPage from "./pages/admin/MediaManagement";
import UserManagementPage from "./pages/admin/UserManagement";
import AnalyticsPage from "./pages/admin/Analytics";
import NotFound from "./components/common/NotFound";
import { AuthProvider } from "./context/AuthContext";
import { MediaProvider } from "./context/MediaContext";
import { RatingProvider } from "./context/RatingContext";
import { AnalyticsProvider } from "./context/AnalyticsContext";
import { AdminProvider } from "./context/AdminContext";

function App() {
  return (
    <AuthProvider>
      <MediaProvider>
        <RatingProvider>
          <AnalyticsProvider>
            <AdminProvider>
              <Router>
                <Layout>
                  <Routes>
                    <Route path="/" element={<Home />} />
                    <Route path="/media/:id" element={<MediaPlayerPage />} />
                    <Route path="/login" element={<LoginPage />} />
                    <Route path="/admin" element={<DashboardPage />} />
                    <Route path="/admin/media" element={<MediaManagementPage />} />
                    <Route path="/admin/users" element={<UserManagementPage />} />
                    <Route path="/admin/analytics" element={<AnalyticsPage />} />
                    <Route path="*" element={<NotFound />} />
                  </Routes>
                </Layout>
              </Router>
            </AdminProvider>
          </AnalyticsProvider>
        </RatingProvider>
      </MediaProvider>
    </AuthProvider>
  );
}

export default App;
```

### `src\components\admin\AnalyticsDisplay.js`

```js
import React from "react";
import Card from "../common/Card";

const AnalyticsDisplay = ({ analytics }) => (
  <Card className="w-full">
    <h3 className="text-xl font-bold mb-2">Analytics Overview</h3>
    {analytics && analytics.length ? (
      <table className="w-full text-left text-sm">
        <thead>
          <tr>
            <th className="py-2 px-2">Media</th>
            <th className="py-2 px-2">Plays</th>
            <th className="py-2 px-2">Unique Users</th>
            <th className="py-2 px-2">Avg. Duration</th>
          </tr>
        </thead>
        <tbody>
          {analytics.map((row) => (
            <tr key={row.media_id}>
              <td className="py-1 px-2">{row.media_title}</td>
              <td className="py-1 px-2">{row.plays}</td>
              <td className="py-1 px-2">{row.unique_users}</td>
              <td className="py-1 px-2">{row.avg_duration}s</td>
            </tr>
          ))}
        </tbody>
      </table>
    ) : (
      <div className="text-gray-400">No analytics data available.</div>
    )}
  </Card>
);

export default AnalyticsDisplay;
```

### `src\components\admin\Dashboard.js`

```js
import React from "react";
import Loader from "../common/Loader";

const Dashboard = ({ stats, loading }) => {
  if (loading) return <Loader />;
  if (!stats) return <div className="text-gray-400">No dashboard data available.</div>;
  return (
    <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div className="bg-gray-800 rounded-lg p-6 text-center">
        <div className="text-3xl font-bold mb-2">{stats.total_users}</div>
        <div className="text-gray-400">Total Users</div>
      </div>
      <div className="bg-gray-800 rounded-lg p-6 text-center">
        <div className="text-3xl font-bold mb-2">{stats.total_media}</div>
        <div className="text-gray-400">Total Media</div>
      </div>
      <div className="bg-gray-800 rounded-lg p-6 text-center">
        <div className="text-3xl font-bold mb-2">{stats.recent_plays}</div>
        <div className="text-gray-400">Recent Plays</div>
      </div>
    </div>
  );
};

export default Dashboard;
```

### `src\components\admin\MediaForm.js`

```js
import React, { useState } from "react";
import Card from "../common/Card";
import Button from "../common/Button";
import Input from "../common/Input";
import Loader from "../common/Loader";

const MediaForm = ({ onSubmit, loading, error, initial = {} }) => {
  const [title, setTitle] = useState(initial.title || "");
  const [type, setType] = useState(initial.type || "video");
  const [description, setDescription] = useState(initial.description || "");
  const [tags, setTags] = useState(initial.tags ? initial.tags.join(", ") : "");
  const [file, setFile] = useState(null);

  const handleSubmit = (e) => {
    e.preventDefault();
    const formData = new FormData();
    formData.append("title", title);
    formData.append("type", type);
    formData.append("description", description);
    formData.append("tags", tags);
    if (file) formData.append("media_file", file);
    onSubmit(formData);
  };

  return (
    <Card className="max-w-md mx-auto">
      <form onSubmit={handleSubmit}>
        <h2 className="text-2xl font-bold mb-4 text-center">Media Form</h2>
        <div className="mb-4">
          <Input
            type="text"
            placeholder="Title"
            value={title}
            onChange={(e) => setTitle(e.target.value)}
            required
          />
        </div>
        <div className="mb-4">
          <textarea
            className="w-full px-3 py-2 rounded border border-gray-700 bg-gray-900 text-white"
            placeholder="Description"
            value={description}
            onChange={(e) => setDescription(e.target.value)}
          />
        </div>
        <div className="mb-4">
          <select
            className="w-full px-3 py-2 rounded border border-gray-700 bg-gray-900 text-white"
            value={type}
            onChange={(e) => setType(e.target.value)}
          >
            <option value="video">Video</option>
            <option value="audio">Audio</option>
          </select>
        </div>
        <div className="mb-4">
          <Input
            type="file"
            accept="video/*,audio/*"
            onChange={(e) => setFile(e.target.files[0])}
          />
        </div>
        <div className="mb-4">
          <Input
            type="text"
            placeholder="Tags (comma separated)"
            value={tags}
            onChange={(e) => setTags(e.target.value)}
          />
        </div>
        {error && <div className="text-red-400 mb-2">{error}</div>}
        <Button type="submit" className="w-full" disabled={loading}>
          {loading ? <Loader className="h-5 w-5" /> : "Submit"}
        </Button>
      </form>
    </Card>
  );
};

export default MediaForm;
```

### `src\components\admin\MediaTable.js`

```js
import React from "react";
import Card from "../common/Card";
import Button from "../common/Button";

const MediaTable = ({ media, onEdit, onDelete }) => (
  <Card>
    <h3 className="text-xl font-bold mb-2">Media</h3>
    <table className="w-full text-left text-sm">
      <thead>
        <tr>
          <th className="py-2 px-2">Title</th>
          <th className="py-2 px-2">Type</th>
          <th className="py-2 px-2">Description</th>
          <th className="py-2 px-2">Actions</th>
        </tr>
      </thead>
      <tbody>
        {media.map((item) => (
          <tr key={item.id}>
            <td className="py-1 px-2">{item.title}</td>
            <td className="py-1 px-2">{item.type}</td>
            <td className="py-1 px-2">{item.description}</td>
            <td className="py-1 px-2 space-x-2">
              <Button onClick={() => onEdit(item)} className="bg-yellow-500 hover:bg-yellow-600 text-xs px-2 py-1">Edit</Button>
              <Button onClick={() => onDelete(item.id)} className="bg-red-500 hover:bg-red-600 text-xs px-2 py-1">Delete</Button>
            </td>
          </tr>
        ))}
      </tbody>
    </table>
  </Card>
);

export default MediaTable;
```

### `src\components\admin\UserTable.js`

```js
import React from "react";
import Card from "../common/Card";
import Button from "../common/Button";

const UserTable = ({ users, onEdit, onDelete }) => (
  <Card>
    <h3 className="text-xl font-bold mb-2">Users</h3>
    <table className="w-full text-left text-sm">
      <thead>
        <tr>
          <th className="py-2 px-2">Username</th>
          <th className="py-2 px-2">Email</th>
          <th className="py-2 px-2">Role</th>
          <th className="py-2 px-2">Actions</th>
        </tr>
      </thead>
      <tbody>
        {users.map((user) => (
          <tr key={user.id}>
            <td className="py-1 px-2">{user.username}</td>
            <td className="py-1 px-2">{user.email}</td>
            <td className="py-1 px-2">{user.role}</td>
            <td className="py-1 px-2 space-x-2">
              <Button onClick={() => onEdit(user)} className="bg-yellow-500 hover:bg-yellow-600 text-xs px-2 py-1">Edit</Button>
              <Button onClick={() => onDelete(user.id)} className="bg-red-500 hover:bg-red-600 text-xs px-2 py-1">Delete</Button>
            </td>
          </tr>
        ))}
      </tbody>
    </table>
  </Card>
);

export default UserTable;
```

### `src\components\auth\LoginForm.js`

```js
import React, { useState } from "react";
import Button from "../common/Button";
import Input from "../common/Input";
import Loader from "../common/Loader";

const LoginForm = ({ onLogin, loading, error }) => {
  const [username, setUsername] = useState("");
  const [password, setPassword] = useState("");

  const handleSubmit = (e) => {
    e.preventDefault();
    onLogin(username, password);
  };

  return (
    <form onSubmit={handleSubmit} className="max-w-sm mx-auto bg-gray-800 p-6 rounded shadow">
      <h2 className="text-2xl font-bold mb-4 text-center">Login</h2>
      <div className="mb-4">
        <Input
          type="text"
          placeholder="Username or Email"
          value={username}
          onChange={(e) => setUsername(e.target.value)}
          required
        />
      </div>
      <div className="mb-4">
        <Input
          type="password"
          placeholder="Password"
          value={password}
          onChange={(e) => setPassword(e.target.value)}
          required
        />
      </div>
      {error && <div className="text-red-400 mb-2">{error}</div>}
      <Button type="submit" className="w-full" disabled={loading}>
        {loading ? <Loader className="h-5 w-5" /> : "Login"}
      </Button>
    </form>
  );
};

export default LoginForm;
```

### `src\components\auth\RegisterForm.js`

```js
import React, { useState } from "react";
import Card from "../common/Card";
import Button from "../common/Button";
import Input from "../common/Input";
import Loader from "../common/Loader";

const RegisterForm = ({ onRegister, loading, error }) => {
  const [username, setUsername] = useState("");
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [role, setRole] = useState("user");

  const handleSubmit = (e) => {
    e.preventDefault();
    onRegister({ username, email, password, role });
  };

  return (
    <Card className="max-w-sm mx-auto">
      <form onSubmit={handleSubmit}>
        <h2 className="text-2xl font-bold mb-4 text-center">Register</h2>
        <div className="mb-4">
          <Input
            type="text"
            placeholder="Username"
            value={username}
            onChange={(e) => setUsername(e.target.value)}
            required
          />
        </div>
        <div className="mb-4">
          <Input
            type="email"
            placeholder="Email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            required
          />
        </div>
        <div className="mb-4">
          <Input
            type="password"
            placeholder="Password"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            required
          />
        </div>
        <div className="mb-4">
          <select
            className="w-full px-3 py-2 rounded border border-gray-700 bg-gray-900 text-white"
            value={role}
            onChange={(e) => setRole(e.target.value)}
          >
            <option value="user">User</option>
            <option value="admin">Admin</option>
          </select>
        </div>
        {error && <div className="text-red-400 mb-2">{error}</div>}
        <Button type="submit" className="w-full" disabled={loading}>
          {loading ? <Loader className="h-5 w-5" /> : "Register"}
        </Button>
      </form>
    </Card>
  );
};

export default RegisterForm;
```

### `src\components\common\Button.js`

```js
import React from "react";

const Button = ({ children, className = "", ...props }) => (
  <button
    className={`px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white font-semibold transition ${className}`}
    {...props}
  >
    {children}
  </button>
);

export default Button;
```

### `src\components\common\Card.js`

```js
import React from "react";

const Card = ({ children, className = "" }) => (
  <div className={`bg-gray-800 rounded-lg shadow p-4 ${className}`}>
    {children}
  </div>
);

export default Card;
```

### `src\components\common\Input.js`

```js
import React from "react";

const Input = ({ className = "", ...props }) => (
  <input
    className={`px-3 py-2 rounded border border-gray-700 bg-gray-900 text-white focus:outline-none focus:ring focus:border-blue-500 ${className}`}
    {...props}
  />
);

export default Input;
```

### `src\components\common\Loader.js`

```js
import React from "react";

const Loader = ({ className = "" }) => (
  <div className={`flex items-center justify-center ${className}`}>
    <svg className="animate-spin h-6 w-6 text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
      <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
      <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
    </svg>
  </div>
);

export default Loader;
```

### `src\components\common\NotFound.js`

```js
import React from "react";
import Loader from "../common/Loader";

const NotFound = () => (
  <div className="flex items-center justify-center min-h-screen bg-gray-900 text-white">
    <div className="text-center">
      <h1 className="text-5xl font-bold mb-4">404</h1>
      <p className="text-lg mb-2">Page not found</p>
      <a href="/" className="text-blue-400 hover:underline">Go Home</a>
    </div>
  </div>
);

export default NotFound;
```

### `src\components\layout\Footer.js`

```js
import React from "react";

const Footer = () => (
  <footer className="bg-gray-800 text-gray-400 text-center py-3 mt-auto">
    &copy; {new Date().getFullYear()} Media Manager PWA
  </footer>
);

export default Footer;
```

### `src\components\layout\Header.js`

```js
import React from "react";

const Header = () => (
  <header className="bg-gray-800 shadow flex items-center justify-between px-6 py-3">
    <div className="font-bold text-xl tracking-wide">Media Manager</div>
    <nav className="space-x-4">
      <a href="/" className="hover:text-blue-400">Home</a>
      <a href="/media" className="hover:text-blue-400">Media</a>
      <a href="/admin" className="hover:text-blue-400">Admin</a>
    </nav>
  </header>
);

export default Header;
```

### `src\components\layout\Layout.js`

```js
import React from "react";
import Header from "./Header";
import Footer from "./Footer";
import Sidebar from "./Sidebar";

const Layout = ({ children }) => (
  <div className="min-h-screen flex flex-col bg-gray-900 text-white">
    <Header />
    <div className="flex flex-1">
      <Sidebar />
      <main className="flex-1 p-4">{children}</main>
    </div>
    <Footer />
  </div>
);

export default Layout;
```

### `src\components\layout\Sidebar.js`

```js
import React from "react";

const Sidebar = () => (
  <aside className="hidden md:block w-56 bg-gray-850 border-r border-gray-800 p-4">
    <nav className="flex flex-col space-y-2">
      <a href="/" className="hover:text-blue-400">Home</a>
      <a href="/media" className="hover:text-blue-400">Browse Media</a>
      <a href="/admin" className="hover:text-blue-400">Admin Dashboard</a>
    </nav>
  </aside>
);

export default Sidebar;
```

### `src\components\media\MediaList.js`

```js
import React from "react";
import Card from "../common/Card";
import Loader from "../common/Loader";

const MediaList = ({ media, loading }) => {
  if (loading) return <Loader />;
  if (!media.length) return <div className="text-gray-400">No media found.</div>;
  return (
    <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
      {media.map((item) => (
        <Card key={item.id} className="hover:shadow-lg transition">
          <div className="font-semibold text-lg mb-2">{item.title}</div>
          <div className="text-sm text-gray-400 mb-2">{item.type}</div>
          <div className="mb-2">{item.description}</div>
          <a href={`/media/${item.id}`} className="text-blue-400 hover:underline">View</a>
        </Card>
      ))}
    </div>
  );
};

export default MediaList;
```

### `src\components\media\MediaPlayer.js`

```js
import React from "react";
import Card from "../common/Card";
import Loader from "../common/Loader";

const MediaPlayer = ({ media, loading }) => {
  if (loading) return <Loader />;
  if (!media) return <div className="text-gray-400">Media not found.</div>;
  return (
    <Card className="w-full max-w-2xl mx-auto">
      <div className="font-semibold text-lg mb-2">{media.title}</div>
      <div className="mb-2">{media.description}</div>
      <div className="mb-4">{media.type === "video" ? (
        <video controls className="w-full rounded">
          <source src={media.url} type="video/mp4" />
        </video>
      ) : (
        <audio controls className="w-full">
          <source src={media.url} type="audio/mpeg" />
        </audio>
      )}</div>
      <div className="text-sm text-gray-400">Tags: {media.tags?.join(", ")}</div>
    </Card>
  );
};

export default MediaPlayer;
```

### `src\components\media\Rating.js`

```js
import React from "react";
import Card from "../common/Card";

const Rating = ({ rating, onRate }) => (
  <Card className="flex items-center space-x-2">
    {[1, 2, 3, 4, 5].map((star) => (
      <button
        key={star}
        onClick={() => onRate(star)}
        className={
          star <= rating
            ? "text-yellow-400 text-2xl"
            : "text-gray-500 text-2xl hover:text-yellow-300"
        }
        aria-label={`Rate ${star} star${star > 1 ? "s" : ""}`}
      >
        ★
      </button>
    ))}
  </Card>
);

export default Rating;
```

### `src\context\AdminContext.js`

```js
import React, { createContext, useState, useContext } from "react";
import { listUsers, createUser, updateUser, deleteUser, getDashboard } from "../services/admin";

const AdminContext = createContext();

export const AdminProvider = ({ children }) => {
  const [users, setUsers] = useState([]);
  const [dashboard, setDashboard] = useState(null);

  const fetchUsers = async () => {
    const data = await listUsers();
    setUsers(data);
    return data;
  };

  const fetchDashboard = async () => {
    const stats = await getDashboard();
    setDashboard(stats);
    return stats;
  };

  return (
    <AdminContext.Provider value={{ users, fetchUsers, createUser, updateUser, deleteUser, dashboard, fetchDashboard }}>
      {children}
    </AdminContext.Provider>
  );
};

export const useAdmin = () => useContext(AdminContext);
```

### `src\context\AnalyticsContext.js`

```js
import React, { createContext, useState, useContext } from "react";
import { trackStart, trackEnd, trackSkip, getAnalytics } from "../services/analytics";

const AnalyticsContext = createContext();

export const AnalyticsProvider = ({ children }) => {
  const [analytics, setAnalytics] = useState([]);

  const fetchAnalytics = async (filters = {}) => {
    const data = await getAnalytics(filters);
    setAnalytics(data);
    return data;
  };

  return (
    <AnalyticsContext.Provider value={{ analytics, fetchAnalytics, trackStart, trackEnd, trackSkip }}>
      {children}
    </AnalyticsContext.Provider>
  );
};

export const useAnalytics = () => useContext(AnalyticsContext);
```

### `src\context\AuthContext.js`

```js
import React, { createContext, useState, useEffect, useContext } from "react";
import { login, logout, getToken, getCurrentUser, isAuthenticated } from "../services/auth";

const AuthContext = createContext();

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (isAuthenticated()) {
      getCurrentUser().then(setUser).finally(() => setLoading(false));
    } else {
      setLoading(false);
    }
  }, []);

  const handleLogin = async (username, password) => {
    const data = await login(username, password);
    setUser(data.user);
    return data;
  };

  const handleLogout = () => {
    logout();
    setUser(null);
  };

  return (
    <AuthContext.Provider value={{ user, loading, login: handleLogin, logout: handleLogout, isAuthenticated }}>
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => useContext(AuthContext);
```

### `src\context\MediaContext.js`

```js
import React, { createContext, useState, useEffect, useContext } from "react";
import { fetchMediaList, fetchMediaById } from "../services/media";

const MediaContext = createContext();

export const MediaProvider = ({ children }) => {
  const [media, setMedia] = useState([]);
  const [loading, setLoading] = useState(false);

  const loadMedia = async (filters = {}) => {
    setLoading(true);
    const items = await fetchMediaList(filters);
    setMedia(items);
    setLoading(false);
  };

  const getMediaById = async (id) => {
    return await fetchMediaById(id);
  };

  return (
    <MediaContext.Provider value={{ media, loading, loadMedia, getMediaById }}>
      {children}
    </MediaContext.Provider>
  );
};

export const useMedia = () => useContext(MediaContext);
```

### `src\context\RatingContext.js`

```js
import React, { createContext, useState, useContext } from "react";
import { rateMedia, getUserRating } from "../services/rating";

const RatingContext = createContext();

export const RatingProvider = ({ children }) => {
  const [userRatings, setUserRatings] = useState({});

  const rate = async (mediaId, rating) => {
    const data = await rateMedia(mediaId, rating);
    setUserRatings((prev) => ({ ...prev, [mediaId]: rating }));
    return data;
  };

  const fetchUserRating = async (mediaId) => {
    const rating = await getUserRating(mediaId);
    setUserRatings((prev) => ({ ...prev, [mediaId]: rating }));
    return rating;
  };

  return (
    <RatingContext.Provider value={{ userRatings, rate, fetchUserRating }}>
      {children}
    </RatingContext.Provider>
  );
};

export const useRating = () => useContext(RatingContext);
```

### `src\index.js`

```js
import React from "react";
import ReactDOM from "react-dom/client";
import "../src/input.css";
import App from "./App";

const root = ReactDOM.createRoot(document.getElementById("root"));
root.render(
  <React.StrictMode>
    <App />
  </React.StrictMode>
);
```

### `src\input.css`

```css
@tailwind base;
@tailwind components;
@tailwind utilities;
```

### `src\pages\Home.js`

```js
import React from "react";
import MediaList from "../components/media/MediaList";
import { useMedia } from "../context/MediaContext";
import Loader from "../components/common/Loader";

const Home = () => {
  const { media, loading, loadMedia } = useMedia();

  React.useEffect(() => {
    loadMedia();
    // eslint-disable-next-line
  }, []);

  return (
    <div className="max-w-5xl mx-auto py-8">
      <h1 className="text-3xl font-bold mb-6">Browse Media</h1>
      <MediaList media={media} loading={loading} />
    </div>
  );
};

export default Home;
```

### `src\pages\Login.js`

```js
import React, { useState } from "react";
import LoginForm from "../components/auth/LoginForm";
import { useAuth } from "../context/AuthContext";
import { useNavigate } from "react-router-dom";

const LoginPage = () => {
  const { login } = useAuth();
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");
  const navigate = useNavigate();

  const handleLogin = async (username, password) => {
    setLoading(true);
    setError("");
    try {
      await login(username, password);
      navigate("/");
    } catch (e) {
      setError(e.response?.data?.error || "Login failed");
    } finally {
      setLoading(false);
    }
  };

  return <LoginForm onLogin={handleLogin} loading={loading} error={error} />;
};

export default LoginPage;
```

### `src\pages\MediaPlayer.js`

```js
import React from "react";
import { useParams } from "react-router-dom";
import { useMedia } from "../context/MediaContext";
import MediaPlayer from "../components/media/MediaPlayer";
import Loader from "../components/common/Loader";

const MediaPlayerPage = () => {
  const { id } = useParams();
  const { getMediaById } = useMedia();
  const [media, setMedia] = React.useState(null);
  const [loading, setLoading] = React.useState(true);

  React.useEffect(() => {
    setLoading(true);
    getMediaById(id).then((data) => {
      setMedia(data);
      setLoading(false);
    });
    // eslint-disable-next-line
  }, [id]);

  return (
    <div className="max-w-3xl mx-auto py-8">
      <MediaPlayer media={media} loading={loading} />
    </div>
  );
};

export default MediaPlayerPage;
```

### `src\pages\admin\Analytics.js`

```js
import React from "react";
import AnalyticsDisplay from "../../components/admin/AnalyticsDisplay";
import { useAnalytics } from "../../context/AnalyticsContext";
import Loader from "../../components/common/Loader";

const AnalyticsPage = () => {
  const { analytics, fetchAnalytics } = useAnalytics();
  const [loading, setLoading] = React.useState(true);

  React.useEffect(() => {
    fetchAnalytics().finally(() => setLoading(false));
    // eslint-disable-next-line
  }, []);

  return (
    <div className="max-w-5xl mx-auto py-8">
      <h1 className="text-3xl font-bold mb-6">Analytics</h1>
      <AnalyticsDisplay analytics={analytics} />
      {loading && <Loader />}
    </div>
  );
};

export default AnalyticsPage;
```

### `src\pages\admin\Dashboard.js`

```js
import React from "react";
import Dashboard from "../../components/admin/Dashboard";
import { useAdmin } from "../../context/AdminContext";
import Loader from "../../components/common/Loader";

const DashboardPage = () => {
  const { dashboard, fetchDashboard } = useAdmin();
  const [loading, setLoading] = React.useState(true);

  React.useEffect(() => {
    fetchDashboard().finally(() => setLoading(false));
    // eslint-disable-next-line
  }, []);

  return (
    <div className="max-w-5xl mx-auto py-8">
      <h1 className="text-3xl font-bold mb-6">Admin Dashboard</h1>
      <Dashboard stats={dashboard} loading={loading} />
    </div>
  );
};

export default DashboardPage;
```

### `src\pages\admin\MediaManagement.js`

```js
import React, { useState } from "react";
import MediaTable from "../../components/admin/MediaTable";
import MediaForm from "../../components/admin/MediaForm";
import { useMedia } from "../../context/MediaContext";
import Loader from "../../components/common/Loader";

const MediaManagementPage = () => {
  const { media, loading, loadMedia } = useMedia();
  const [editing, setEditing] = useState(null);
  const [showForm, setShowForm] = useState(false);

  React.useEffect(() => {
    loadMedia();
    // eslint-disable-next-line
  }, []);

  const handleEdit = (item) => {
    setEditing(item);
    setShowForm(true);
  };

  const handleDelete = (id) => {
    // TODO: Add delete logic
    alert(`Delete media with id ${id}`);
  };

  const handleFormSubmit = (formData) => {
    // TODO: Add create/update logic
    setShowForm(false);
    setEditing(null);
  };

  return (
    <div className="max-w-5xl mx-auto py-8">
      <h1 className="text-3xl font-bold mb-6">Media Management</h1>
      <button className="mb-4 px-4 py-2 rounded bg-blue-600 text-white" onClick={() => setShowForm(true)}>
        Add New Media
      </button>
      {showForm && (
        <MediaForm onSubmit={handleFormSubmit} initial={editing} />
      )}
      <MediaTable media={media} onEdit={handleEdit} onDelete={handleDelete} />
      {loading && <Loader />}
    </div>
  );
};

export default MediaManagementPage;
```

### `src\pages\admin\UserManagement.js`

```js
import React, { useState } from "react";
import UserTable from "../../components/admin/UserTable";
import { useAdmin } from "../../context/AdminContext";
import Loader from "../../components/common/Loader";

const UserManagementPage = () => {
  const { users, fetchUsers, createUser, updateUser, deleteUser } = useAdmin();
  const [loading, setLoading] = useState(true);
  const [editing, setEditing] = useState(null);

  React.useEffect(() => {
    fetchUsers().finally(() => setLoading(false));
    // eslint-disable-next-line
  }, []);

  const handleEdit = (user) => {
    setEditing(user);
    // TODO: Show user form for editing
  };

  const handleDelete = (id) => {
    // TODO: Add delete logic
    alert(`Delete user with id ${id}`);
  };

  return (
    <div className="max-w-5xl mx-auto py-8">
      <h1 className="text-3xl font-bold mb-6">User Management</h1>
      <UserTable users={users} onEdit={handleEdit} onDelete={handleDelete} />
      {loading && <Loader />}
    </div>
  );
};

export default UserManagementPage;
```

### `src\services\admin.js`

```js
import api from "./api";
import { getToken } from "./auth";

export const listUsers = async () => {
  const { data } = await api.get("/admin/users", {
    headers: { Authorization: `Bearer ${getToken()}` },
  });
  return data.users;
};

export const createUser = async (userData) => {
  const { data } = await api.post("/admin/users", userData, {
    headers: { Authorization: `Bearer ${getToken()}` },
  });
  return data;
};

export const updateUser = async (id, userData) => {
  const { data } = await api.put(`/admin/users/${id}`, userData, {
    headers: { Authorization: `Bearer ${getToken()}` },
  });
  return data;
};

export const deleteUser = async (id) => {
  const { data } = await api.delete(`/admin/users/${id}`, {
    headers: { Authorization: `Bearer ${getToken()}` },
  });
  return data;
};

export const getDashboard = async () => {
  const { data } = await api.get("/admin/dashboard", {
    headers: { Authorization: `Bearer ${getToken()}` },
  });
  return data.stats;
};
```

### `src\services\analytics.js`

```js
import api from "./api";
import { getToken } from "./auth";

export const trackStart = async (payload) => {
  const { data } = await api.post("/analytics/start", payload, {
    headers: { Authorization: `Bearer ${getToken()}` },
  });
  return data;
};

export const trackEnd = async (payload) => {
  const { data } = await api.post("/analytics/end", payload, {
    headers: { Authorization: `Bearer ${getToken()}` },
  });
  return data;
};

export const trackSkip = async (payload) => {
  const { data } = await api.post("/analytics/skip", payload, {
    headers: { Authorization: `Bearer ${getToken()}` },
  });
  return data;
};

export const getAnalytics = async (params = {}) => {
  const { data } = await api.get("/analytics", {
    params,
    headers: { Authorization: `Bearer ${getToken()}` },
  });
  return data.analytics;
};
```

### `src\services\api.js`

```js
import axios from "axios";

const api = axios.create({
  baseURL: process.env.REACT_APP_API_URL || "http://localhost:8000/api",
  headers: {
    "Content-Type": "application/json",
  },
});

export default api;
```

### `src\services\auth.js`

```js
import api from "./api";

const TOKEN_KEY = "media_manager_token";

export const login = async (username, password) => {
  const { data } = await api.post("/auth/login", { username, password });
  if (data.token) localStorage.setItem(TOKEN_KEY, data.token);
  return data;
};

export const logout = () => {
  localStorage.removeItem(TOKEN_KEY);
};

export const getToken = () => localStorage.getItem(TOKEN_KEY);

export const isAuthenticated = () => !!getToken();

export const getCurrentUser = async () => {
  const { data } = await api.get("/auth/user", {
    headers: { Authorization: `Bearer ${getToken()}` },
  });
  return data.user;
};
```

### `src\services\media.js`

```js
import api from "./api";
import { getToken } from "./auth";

export const fetchMediaList = async (params = {}) => {
  const { data } = await api.get("/media", { params });
  return data.media;
};

export const fetchMediaById = async (id) => {
  const { data } = await api.get(`/media/${id}`);
  return data.media;
};

export const playMedia = async (id) => {
  const { data } = await api.get(`/media/${id}/play`);
  return data.play_url;
};

export const addMedia = async (mediaData) => {
  const { data } = await api.post("/media", mediaData, {
    headers: { Authorization: `Bearer ${getToken()}` },
  });
  return data;
};

export const updateMedia = async (id, mediaData) => {
  const { data } = await api.put(`/media/${id}`, mediaData, {
    headers: { Authorization: `Bearer ${getToken()}` },
  });
  return data;
};

export const deleteMedia = async (id) => {
  const { data } = await api.delete(`/media/${id}`, {
    headers: { Authorization: `Bearer ${getToken()}` },
  });
  return data;
};
```

### `src\services\rating.js`

```js
import api from "./api";
import { getToken } from "./auth";

export const rateMedia = async (mediaId, rating) => {
  const { data } = await api.post(
    `/media/${mediaId}/rate`,
    { rating },
    { headers: { Authorization: `Bearer ${getToken()}` } }
  );
  return data;
};

export const getUserRating = async (mediaId) => {
  const { data } = await api.get(`/media/${mediaId}/rating`, {
    headers: { Authorization: `Bearer ${getToken()}` },
  });
  return data.rating;
};
```

### `tailwind.config.js`

```js
/** @type {import('tailwindcss').Config} */
module.exports = {
    content: ["./src/**/*.{html,js}"],
    theme: {
      extend: {},
    },
    plugins: [],
  }
```
