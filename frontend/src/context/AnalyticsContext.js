import React, { createContext, useState, useContext } from "react";
import { trackStart, trackEnd, trackSkip, getAnalytics } from "../services/analytics";

const AnalyticsContext = createContext();

export const AnalyticsProvider = ({ children }) => {
  const [analytics, setAnalytics] = useState([]);

  const fetchAnalytics = async (filters = {}) => {
    const data = await getAnalytics(filters);
    setAnalytics(data);
    return data;
  };

  return (
    <AnalyticsContext.Provider value={{ analytics, fetchAnalytics, trackStart, trackEnd, trackSkip }}>
      {children}
    </AnalyticsContext.Provider>
  );
};

export const useAnalytics = () => useContext(AnalyticsContext);
