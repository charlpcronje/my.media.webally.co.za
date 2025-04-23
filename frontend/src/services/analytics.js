import api from "./api";
import { getToken } from "./auth";

export const trackStart = async (payload) => {
  const { data } = await api.post("/analytics/start", payload, {
    headers: { Authorization: `Bearer ${getToken()}` },
  });
  return data;
};

export const trackEnd = async (payload) => {
  const { data } = await api.post("/analytics/end", payload, {
    headers: { Authorization: `Bearer ${getToken()}` },
  });
  return data;
};

export const trackSkip = async (payload) => {
  const { data } = await api.post("/analytics/skip", payload, {
    headers: { Authorization: `Bearer ${getToken()}` },
  });
  return data;
};

export const getAnalytics = async (params = {}) => {
  const { data } = await api.get("/analytics", {
    params,
    headers: { Authorization: `Bearer ${getToken()}` },
  });
  return data.analytics;
};
