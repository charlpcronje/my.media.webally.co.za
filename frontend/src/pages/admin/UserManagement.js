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
