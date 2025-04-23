import api from "./api";
import { getToken } from "./auth";

export const fetchMediaList = async (params = {}) => {
  const { data } = await api.get("/media", { params });
  return data.media;
};

export const fetchMediaById = async (id) => {
  const { data } = await api.get(`/media/${id}`);
  return data.media;
};

export const playMedia = async (id) => {
  const { data } = await api.get(`/media/${id}/play`);
  return data.play_url;
};

export const addMedia = async (mediaData) => {
  const { data } = await api.post("/media", mediaData, {
    headers: { Authorization: `Bearer ${getToken()}` },
  });
  return data;
};

export const updateMedia = async (id, mediaData) => {
  const { data } = await api.put(`/media/${id}`, mediaData, {
    headers: { Authorization: `Bearer ${getToken()}` },
  });
  return data;
};

export const deleteMedia = async (id) => {
  const { data } = await api.delete(`/media/${id}`, {
    headers: { Authorization: `Bearer ${getToken()}` },
  });
  return data;
};
