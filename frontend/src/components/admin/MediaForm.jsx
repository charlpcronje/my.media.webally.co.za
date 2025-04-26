import React, { useState } from "react";
import Card from "../common/Card";
import Button from "../common/Button";
import Input from "../common/Input";
import Loader from "../common/Loader";

const MediaForm = ({ onSubmit, loading, error, initial = {} }) => {
  const [title, setTitle] = useState(initial.title || "");
  const [type, setType] = useState(initial.type || "video");
  const [description, setDescription] = useState(initial.description || "");
  const [tags, setTags] = useState(initial.tags ? initial.tags.join(", ") : "");
  const [file, setFile] = useState(null);

  const handleSubmit = (e) => {
    e.preventDefault();
    const formData = new FormData();
    formData.append("title", title);
    formData.append("type", type);
    formData.append("description", description);
    formData.append("tags", tags);
    if (file) formData.append("media_file", file);
    onSubmit(formData);
  };

  return (
    <Card className="max-w-md mx-auto">
      <form onSubmit={handleSubmit}>
        <h2 className="text-2xl font-bold mb-4 text-center">Media Form</h2>
        <div className="mb-4">
          <Input
            type="text"
            placeholder="Title"
            value={title}
            onChange={(e) => setTitle(e.target.value)}
            required
          />
        </div>
        <div className="mb-4">
          <textarea
            className="w-full px-3 py-2 rounded border border-gray-700 bg-gray-900 text-white"
            placeholder="Description"
            value={description}
            onChange={(e) => setDescription(e.target.value)}
          />
        </div>
        <div className="mb-4">
          <select
            className="w-full px-3 py-2 rounded border border-gray-700 bg-gray-900 text-white"
            value={type}
            onChange={(e) => setType(e.target.value)}
          >
            <option value="video">Video</option>
            <option value="audio">Audio</option>
          </select>
        </div>
        <div className="mb-4">
          <Input
            type="file"
            accept="video/*,audio/*"
            onChange={(e) => setFile(e.target.files[0])}
          />
        </div>
        <div className="mb-4">
          <Input
            type="text"
            placeholder="Tags (comma separated)"
            value={tags}
            onChange={(e) => setTags(e.target.value)}
          />
        </div>
        {error && <div className="text-red-400 mb-2">{error}</div>}
        <Button type="submit" className="w-full" disabled={loading}>
          {loading ? <Loader className="h-5 w-5" /> : "Submit"}
        </Button>
      </form>
    </Card>
  );
};

export default MediaForm;
