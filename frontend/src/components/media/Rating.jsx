import React from "react";
import Card from "../common/Card";

const Rating = ({ rating, onRate }) => (
  <Card className="flex items-center space-x-2">
    {[1, 2, 3, 4, 5].map((star) => (
      <button
        key={star}
        onClick={() => onRate(star)}
        className={
          star <= rating
            ? "text-yellow-400 text-2xl"
            : "text-gray-500 text-2xl hover:text-yellow-300"
        }
        aria-label={`Rate ${star} star${star > 1 ? "s" : ""}`}
      >
        ★
      </button>
    ))}
  </Card>
);

export default Rating;
