import React from "react";

const Header = () => (
  <header className="bg-gray-800 shadow flex items-center justify-between px-6 py-3">
    <div className="font-bold text-xl tracking-wide">Media Manager</div>
    <nav className="space-x-4">
      <a href="/" className="hover:text-blue-400">Home</a>
      <a href="/media" className="hover:text-blue-400">Media</a>
      <a href="/admin" className="hover:text-blue-400">Admin</a>
    </nav>
  </header>
);

export default Header;
