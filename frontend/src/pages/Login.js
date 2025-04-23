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
