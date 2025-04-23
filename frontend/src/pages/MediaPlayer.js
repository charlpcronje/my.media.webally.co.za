import React from "react";
import { useParams } from "react-router-dom";
import { useMedia } from "../../context/MediaContext";
import MediaPlayer from "../media/MediaPlayer";
import Loader from "../common/Loader";

const MediaPlayerPage = () => {
  const { id } = useParams();
  const { getMediaById } = useMedia();
  const [media, setMedia] = React.useState(null);
  const [loading, setLoading] = React.useState(true);

  React.useEffect(() => {
    setLoading(true);
    getMediaById(id).then((data) => {
      setMedia(data);
      setLoading(false);
    });
    // eslint-disable-next-line
  }, [id]);

  return (
    <div className="max-w-3xl mx-auto py-8">
      <MediaPlayer media={media} loading={loading} />
    </div>
  );
};

export default MediaPlayerPage;
