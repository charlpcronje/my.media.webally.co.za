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
