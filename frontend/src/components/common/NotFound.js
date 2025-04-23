import React from "react";
import Loader from "../common/Loader";

const NotFound = () => (
  <div className="flex items-center justify-center min-h-screen bg-gray-900 text-white">
    <div className="text-center">
      <h1 className="text-5xl font-bold mb-4">404</h1>
      <p className="text-lg mb-2">Page not found</p>
      <a href="/" className="text-blue-400 hover:underline">Go Home</a>
    </div>
  </div>
);

export default NotFound;
