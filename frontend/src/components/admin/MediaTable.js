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
