import React, { useState } from "react";
import MediaTable from "../../components/admin/MediaTable";
import MediaForm from "../../components/admin/MediaForm";
import { useMedia } from "../../context/MediaContext";
import Loader from "../../components/common/Loader";

const MediaManagementPage = () => {
  const { media, loading, loadMedia } = useMedia();
  const [editing, setEditing] = useState(null);
  const [showForm, setShowForm] = useState(false);

  React.useEffect(() => {
    loadMedia();
    // eslint-disable-next-line
  }, []);

  const handleEdit = (item) => {
    setEditing(item);
    setShowForm(true);
  };

  const handleDelete = (id) => {
    // TODO: Add delete logic
    alert(`Delete media with id ${id}`);
  };

  const handleFormSubmit = (formData) => {
    // TODO: Add create/update logic
    setShowForm(false);
    setEditing(null);
  };

  return (
    <div className="max-w-5xl mx-auto py-8">
      <h1 className="text-3xl font-bold mb-6">Media Management</h1>
      <button className="mb-4 px-4 py-2 rounded bg-blue-600 text-white" onClick={() => setShowForm(true)}>
        Add New Media
      </button>
      {showForm && (
        <MediaForm onSubmit={handleFormSubmit} initial={editing} />
      )}
      <MediaTable media={media} onEdit={handleEdit} onDelete={handleDelete} />
      {loading && <Loader />}
    </div>
  );
};

export default MediaManagementPage;
