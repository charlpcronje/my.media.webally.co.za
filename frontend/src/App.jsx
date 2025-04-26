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
