import React from "react";
import AnalyticsDisplay from "../../components/admin/AnalyticsDisplay";
import { useAnalytics } from "../../context/AnalyticsContext";
import Loader from "../../components/common/Loader";

const AnalyticsPage = () => {
  const { analytics, fetchAnalytics } = useAnalytics();
  const [loading, setLoading] = React.useState(true);

  React.useEffect(() => {
    fetchAnalytics().finally(() => setLoading(false));
    // eslint-disable-next-line
  }, []);

  return (
    <div className="max-w-5xl mx-auto py-8">
      <h1 className="text-3xl font-bold mb-6">Analytics</h1>
      <AnalyticsDisplay analytics={analytics} />
      {loading && <Loader />}
    </div>
  );
};

export default AnalyticsPage;
