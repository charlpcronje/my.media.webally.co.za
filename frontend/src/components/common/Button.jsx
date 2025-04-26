import React from "react";

const Button = ({ children, className = "", ...props }) => (
  <button
    className={`px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white font-semibold transition ${className}`}
    {...props}
  >
    {children}
  </button>
);

export default Button;
