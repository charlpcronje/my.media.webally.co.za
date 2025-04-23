import React, { createContext, useState, useEffect, useContext } from "react";
import { fetchMediaList, fetchMediaById } from "../services/media";

const MediaContext = createContext();

export const MediaProvider = ({ children }) => {
  const [media, setMedia] = useState([]);
  const [loading, setLoading] = useState(false);

  const loadMedia = async (filters = {}) => {
    setLoading(true);
    const items = await fetchMediaList(filters);
    setMedia(items);
    setLoading(false);
  };

  const getMediaById = async (id) => {
    return await fetchMediaById(id);
  };

  return (
    <MediaContext.Provider value={{ media, loading, loadMedia, getMediaById }}>
      {children}
    </MediaContext.Provider>
  );
};

export const useMedia = () => useContext(MediaContext);
