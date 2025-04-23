import React from "react";
import MediaList from "../media/MediaList";
import { useMedia } from "../../context/MediaContext";
import Loader from "../common/Loader";

const Home = () => {
  const { media, loading, loadMedia } = useMedia();

  React.useEffect(() => {
    loadMedia();
    // eslint-disable-next-line
  }, []);

  return (
    <div className="max-w-5xl mx-auto py-8">
      <h1 className="text-3xl font-bold mb-6">Browse Media</h1>
      <MediaList media={media} loading={loading} />
    </div>
  );
};

export default Home;
