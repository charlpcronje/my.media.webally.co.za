import React, { createContext, useState, useContext } from "react";
import { rateMedia, getUserRating } from "../services/rating";

const RatingContext = createContext();

export const RatingProvider = ({ children }) => {
  const [userRatings, setUserRatings] = useState({});

  const rate = async (mediaId, rating) => {
    const data = await rateMedia(mediaId, rating);
    setUserRatings((prev) => ({ ...prev, [mediaId]: rating }));
    return data;
  };

  const fetchUserRating = async (mediaId) => {
    const rating = await getUserRating(mediaId);
    setUserRatings((prev) => ({ ...prev, [mediaId]: rating }));
    return rating;
  };

  return (
    <RatingContext.Provider value={{ userRatings, rate, fetchUserRating }}>
      {children}
    </RatingContext.Provider>
  );
};

export const useRating = () => useContext(RatingContext);
