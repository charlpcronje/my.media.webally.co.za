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
