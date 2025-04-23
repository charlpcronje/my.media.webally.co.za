import React from "react";
import Card from "../common/Card";
import Loader from "../common/Loader";

const MediaPlayer = ({ media, loading }) => {
  if (loading) return <Loader />;
  if (!media) return <div className="text-gray-400">Media not found.</div>;
  return (
    <Card className="w-full max-w-2xl mx-auto">
      <div className="font-semibold text-lg mb-2">{media.title}</div>
      <div className="mb-2">{media.description}</div>
      <div className="mb-4">{media.type === "video" ? (
        <video controls className="w-full rounded">
          <source src={media.url} type="video/mp4" />
        </video>
      ) : (
        <audio controls className="w-full">
          <source src={media.url} type="audio/mpeg" />
        </audio>
      )}</div>
      <div className="text-sm text-gray-400">Tags: {media.tags?.join(", ")}</div>
    </Card>
  );
};

export default MediaPlayer;
