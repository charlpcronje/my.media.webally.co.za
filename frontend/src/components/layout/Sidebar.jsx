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
