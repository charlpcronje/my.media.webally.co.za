import React from "react";
import Header from "./Header";
import Footer from "./Footer";
import Sidebar from "./Sidebar";

const Layout = ({ children }) => (
  <div className="min-h-screen flex flex-col bg-gray-900 text-white">
    <Header />
    <div className="flex flex-1">
      <Sidebar />
      <main className="flex-1 p-4">{children}</main>
    </div>
    <Footer />
  </div>
);

export default Layout;
