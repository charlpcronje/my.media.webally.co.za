import React from "react";

const Card = ({ children, className = "" }) => (
  <div className={`bg-gray-800 rounded-lg shadow p-4 ${className}`}>
    {children}
  </div>
);

export default Card;
