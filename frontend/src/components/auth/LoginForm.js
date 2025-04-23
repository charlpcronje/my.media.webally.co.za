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
