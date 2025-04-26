import React from "react";
import Loader from "../common/Loader";

const Dashboard = ({ stats, loading }) => {
  if (loading) return <Loader />;
  if (!stats) return <div className="text-gray-400">No dashboard data available.</div>;
  return (
    <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div className="bg-gray-800 rounded-lg p-6 text-center">
        <div className="text-3xl font-bold mb-2">{stats.total_users}</div>
        <div className="text-gray-400">Total Users</div>
      </div>
      <div className="bg-gray-800 rounded-lg p-6 text-center">
        <div className="text-3xl font-bold mb-2">{stats.total_media}</div>
        <div className="text-gray-400">Total Media</div>
      </div>
      <div className="bg-gray-800 rounded-lg p-6 text-center">
        <div className="text-3xl font-bold mb-2">{stats.recent_plays}</div>
        <div className="text-gray-400">Recent Plays</div>
      </div>
    </div>
  );
};

export default Dashboard;
