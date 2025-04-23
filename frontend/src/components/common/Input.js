import React from "react";

const Input = ({ className = "", ...props }) => (
  <input
    className={`px-3 py-2 rounded border border-gray-700 bg-gray-900 text-white focus:outline-none focus:ring focus:border-blue-500 ${className}`}
    {...props}
  />
);

export default Input;
