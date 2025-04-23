import React from "react";
import Dashboard from "../../components/admin/Dashboard";
import { useAdmin } from "../../context/AdminContext";
import Loader from "../../components/common/Loader";

const DashboardPage = () => {
  const { dashboard, fetchDashboard } = useAdmin();
  const [loading, setLoading] = React.useState(true);

  React.useEffect(() => {
    fetchDashboard().finally(() => setLoading(false));
    // eslint-disable-next-line
  }, []);

  return (
    <div className="max-w-5xl mx-auto py-8">
      <h1 className="text-3xl font-bold mb-6">Admin Dashboard</h1>
      <Dashboard stats={dashboard} loading={loading} />
    </div>
  );
};

export default DashboardPage;
