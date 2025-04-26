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
