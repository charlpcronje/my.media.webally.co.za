import api from "./api";
import { getToken } from "./auth";

export const rateMedia = async (mediaId, rating) => {
  const { data } = await api.post(
    `/media/${mediaId}/rate`,
    { rating },
    { headers: { Authorization: `Bearer ${getToken()}` } }
  );
  return data;
};

export const getUserRating = async (mediaId) => {
  const { data } = await api.get(`/media/${mediaId}/rating`, {
    headers: { Authorization: `Bearer ${getToken()}` },
  });
  return data.rating;
};
