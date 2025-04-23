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
