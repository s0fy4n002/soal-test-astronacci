"use client";
import { useState, ChangeEvent } from "react";

type FormType = {
  name: string;
  id: string;
  flightNumber: string;
  date: string;
  aircraft: string;
};

type CheckResponse = {
  exists: boolean;
};

type GenerateResponse = {
  success: boolean;
  errors?: Record<string, string[]>;
  seats: string[];
};

function App() {
  const [form, setForm] = useState<FormType>({
    name: "",
    id: "",
    flightNumber: "",
    date: "",
    aircraft: "ATR",
  });

  const [seats, setSeats] = useState<string[]>([]);
  const [error, setError] = useState<string>("");
  const [errors, setErrors] = useState<Record<string, string[]>>({});
  const [loading, setLoading] = useState<boolean>(false);

  const handleChange = (
    e: ChangeEvent<HTMLInputElement | HTMLSelectElement>,
  ) => {
    setForm({
      ...form,
      [e.target.name]: e.target.value,
    });

    const newErrors = { ...errors };
    delete newErrors[e.target.name];
    setErrors(newErrors);
  };

  const generateVoucher = async () => {
    setError("");
    setSeats([]);
    setLoading(true);

    try {
      const checkResponse = await fetch("http://127.0.0.1:8000/api/check", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
        },
        body: JSON.stringify({
          flightNumber: form.flightNumber,
          date: form.date,
        }),
      });

      const checkData: CheckResponse = await checkResponse.json();

      if (checkData.exists) {
        setError("Voucher already generated for this flight and date.");
        setLoading(false);
        return;
      }

      const generateResponse = await fetch(
        "http://127.0.0.1:8000/api/generate",
        {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(form),
        },
      );

      const generateData: GenerateResponse = await generateResponse.json();

      if (generateData.success) {
        setSeats(generateData.seats);
      } else {
        setErrors(generateData.errors || {});
        setError("Failed to generate voucher");
      }
    } catch (err) {
      setError("Server error");
    }

    setLoading(false);
  };

  return (
    <div className="min-h-screen bg-gray-100 flex items-center justify-center">
      <div className="bg-white shadow-lg rounded-xl w-full max-w-lg p-8">
        <h1 className="text-2xl font-bold mb-6 text-center">
          Airline Voucher Seat Generator
        </h1>

        <div className="space-y-4">
          <div>
            <input
              type="text"
              name="name"
              placeholder="Crew Name"
              className="w-full border rounded-lg p-3"
              onChange={handleChange}
            />
            {errors.name && (
              <p className="text-red-500 text-sm">{errors.name[0]}</p>
            )}
          </div>

          <div>
            <input
              type="text"
              name="id"
              placeholder="Crew ID"
              className="w-full border rounded-lg p-3"
              onChange={handleChange}
            />
            {errors.id && (
              <p className="text-red-500 text-sm">{errors.id[0]}</p>
            )}
          </div>

          <div>
            <input
              type="text"
              name="flightNumber"
              placeholder="Flight Number (GA102)"
              className="w-full border rounded-lg p-3"
              onChange={handleChange}
            />
            {errors.flightNumber && (
              <p className="text-red-500 text-sm">{errors.flightNumber[0]}</p>
            )}
          </div>

          <div>
            <input
              type="date"
              name="date"
              className="w-full border rounded-lg p-3"
              onChange={handleChange}
            />
            {errors.date && (
              <p className="text-red-500 text-sm">{errors.date[0]}</p>
            )}
          </div>

          <div>
            <select
              name="aircraft"
              className="w-full border rounded-lg p-3"
              onChange={handleChange}
            >
              <option value="ATR">ATR</option>
              <option value="Airbus 320">Airbus 320</option>
              <option value="Boeing 737 Max">Boeing 737 Max</option>
            </select>
            {errors.aircraft && (
              <p className="text-red-500 text-sm">{errors.aircraft[0]}</p>
            )}
          </div>

          <button
            onClick={generateVoucher}
            disabled={loading}
            className="w-full bg-blue-600 text-white p-3 rounded-lg hover:bg-blue-700 flex justify-center items-center gap-2"
          >
            {loading && (
              <div className="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
            )}

            {loading ? "Generating..." : "Generate Vouchers"}
          </button>
        </div>

        {error && <div className="mt-4 text-red-600 text-center">{error}</div>}

        {seats.length > 0 && (
          <div className="mt-6">
            <h2 className="text-lg font-semibold mb-3 text-center">
              Generated Seats
            </h2>

            <div className="flex justify-center gap-4">
              {seats.map((seat, index) => (
                <div
                  key={index}
                  className="bg-green-500 text-white px-6 py-4 rounded-lg text-xl font-bold"
                >
                  {seat}
                </div>
              ))}
            </div>
          </div>
        )}
      </div>
    </div>
  );
}

export default App;
