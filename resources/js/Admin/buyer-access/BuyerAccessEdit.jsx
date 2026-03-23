import React, { useEffect, useState } from "react";

export default function BuyerAccessEdit({ requestId }) {
    const [data, setData] = useState(null);
    const [decision, setDecision] = useState("");
    const [note, setNote] = useState("");
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        fetch(`/api/admin/buyer-access/${requestId}`)
            .then((res) => res.json())
            .then((json) => {
                setData(json.data);
                setDecision(json.data.status);
                setNote(json.data.note || "");
                setLoading(false);
            });
    }, [requestId]);

    const save = () => {
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
                alert("Saved");
            });
    };

    if (loading) return <div>Loading...</div>;
    if (!data) return <div>Not found</div>;

    return (
        <div className="container-fluid">
            <h1 className="mb-4">Edit Buyer Access Request #{data.id}</h1>

            <div className="card mb-4">
                <div className="card-body">

                    <h5 className="mb-3">Current Decision</h5>

                    <table className="table table-bordered">
                        <tbody>
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
                                <th>Note</th>
                                <td>{data.note || "-"}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {/* Edit Form */}
            <div className="card">
                <div className="card-body">
                    <h5 className="mb-3">Modify Decision</h5>

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
                            <option value="pending">Reset to Pending</option>
                        </select>
                    </div>

                    <div className="mb-3">
                        <label className="form-label">Note</label>
                        <textarea
                            className="form-control"
                            rows="3"
                            value={note}
                            onChange={(e) => setNote(e.target.value)}
                        ></textarea>
                    </div>

                    <button
                        className="btn btn-primary"
                        onClick={save}
                        disabled={!decision}
                    >
                        Save Changes
                    </button>
                </div>
            </div>
        </div>
    );
}