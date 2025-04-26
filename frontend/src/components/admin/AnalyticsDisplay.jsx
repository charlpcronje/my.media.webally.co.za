import React from "react";
import Card from "../common/Card";

const AnalyticsDisplay = ({ analytics }) => (
  <Card className="w-full">
    <h3 className="text-xl font-bold mb-2">Analytics Overview</h3>
    {analytics && analytics.length ? (
      <table className="w-full text-left text-sm">
        <thead>
          <tr>
            <th className="py-2 px-2">Media</th>
            <th className="py-2 px-2">Plays</th>
            <th className="py-2 px-2">Unique Users</th>
            <th className="py-2 px-2">Avg. Duration</th>
          </tr>
        </thead>
        <tbody>
          {analytics.map((row) => (
            <tr key={row.media_id}>
              <td className="py-1 px-2">{row.media_title}</td>
              <td className="py-1 px-2">{row.plays}</td>
              <td className="py-1 px-2">{row.unique_users}</td>
              <td className="py-1 px-2">{row.avg_duration}s</td>
            </tr>
          ))}
        </tbody>
      </table>
    ) : (
      <div className="text-gray-400">No analytics data available.</div>
    )}
  </Card>
);

export default AnalyticsDisplay;
