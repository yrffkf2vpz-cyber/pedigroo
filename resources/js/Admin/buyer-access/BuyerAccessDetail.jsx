import React, { useEffect, useState } from "react";

export default function BuyerAccessDetail({ requestId }) {
    const [data, setData] = useState(null);
    const [decision, setDecision] = useState("");
    const [note, setNote] = useState("");
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        fetch(`/api/admin/buyer-access/${requestId}`)
            .then((res) => res.json())
            .then((json) => {
                setData(json.data);
                setLoading(false);
            });
    }, [requestId]);

    const submitDecision = () => {
        fetch(`/api/buyer-access/${requestId}/decision`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                decision,
                note,
            }),
        })
            .then((res) => res.json())
            .then((json) => {
                setData(json.data);
                alert("Decision saved");
            });
    };

    if (loading) return <div>Loading...</div>;
    if (!data) return <div>Not found</div>;

    return (
        <div className="container-fluid">
            <h1 className="mb-4">Buyer Access Request #{data.id}</h1>

            <div className="card mb-4">
                <div className="card-body">

                    <h5 className="mb-3">Request Details</h5>

                    <table className="table table-bordered">
                        <tbody>
                            <tr>
                                <th>Buyer</th>
                                <td>
                                    {data.buyer.name}
                                    <br />
                                    <small className="text-muted">#{data.buyer.id}</small>
                                </td>
                            </tr>

                            <tr>
                                <th>Dog</th>
                                <td>
                                    {data.dog.name}
                                    <br />
                                    <small className="text-muted">#{data.dog.id}</small>
                                </td>
                            </tr>

                            <tr>
                                <th>Kennel</th>
                                <td>
                                    {data.kennel.name}
                                    <br />
                                    <small className="text-muted">#{data.kennel.id}</small>
                                </td>
                            </tr>

                            <tr>
                                <th>Purpose</th>
                                <td>{data.purpose || "-"}</td>
                            </tr>

                            <tr>
                                <th>Message</th>
                                <td>{data.message || "-"}</td>
                            </tr>

                            <tr>
                                <th>Status</th>
                                <td>
                                    {data.is_pending && (
                                        <span className="badge bg-warning">Pending</span>
                                    )}
                                    {data.is_approved && (
                                        <span className="badge bg-success">Approved</span>
                                    )}
                                    {data.is_rejected && (
                                        <span className="badge bg-danger">Rejected</span>
                                    )}
                                </td>
                            </tr>

                            <tr>
                                <th>Requested At</th>
                                <td>{data.created_at}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {/* Decision Box */}
            {data.is_pending && (
                <div className="card">
                    <div className="card-body">
                        <h5 className="mb-3">Decision</h5>

                        <div className="mb-3">
                            <label className="form-label">Decision</label>
                            <select
                                className="form-select"
                                value={decision}
                                onChange={(e) => setDecision(e.target.value)}
                            >
                                <option value="">Select...</option>
                                <option value="approved">Approve</option>
                                <option value="rejected">Reject</option>
                            </select>
                        </div>

                        <div className="mb-3">
                            <label className="form-label">Note (optional)</label>
                            <textarea
                                className="form-control"
                                rows="3"
                                value={note}
                                onChange={(e) => setNote(e.target.value)}
                            ></textarea>
                        </div>

                        <button
                            className="btn btn-primary"
                            onClick={submitDecision}
                            disabled={!decision}
                        >
                            Save Decision
                        </button>
                    </div>
                </div>
            )}
        </div>
    );
}