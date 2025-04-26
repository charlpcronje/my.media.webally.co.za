import React from "react";
import Card from "../common/Card";
import Loader from "../common/Loader";

const MediaList = ({ media, loading }) => {
  if (loading) return <Loader />;
  if (!media.length) return <div className="text-gray-400">No media found.</div>;
  return (
    <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
      {media.map((item) => (
        <Card key={item.id} className="hover:shadow-lg transition">
          <div className="font-semibold text-lg mb-2">{item.title}</div>
          <div className="text-sm text-gray-400 mb-2">{item.type}</div>
          <div className="mb-2">{item.description}</div>
          <a href={`/media/${item.id}`} className="text-blue-400 hover:underline">View</a>
        </Card>
      ))}
    </div>
  );
};

export default MediaList;
