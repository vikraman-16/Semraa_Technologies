"use client";

import { useState } from "react";
import ResponsiveBox from "@/components/core/ResponsiveBox";
import ConstraintedBox from "@/components/core/ConstraintedBox";

const GOOGLE_SCRIPT_URL =
  "https://script.google.com/macros/s/AKfycbwe6HB9qElRX1ax2PWieY6cClPwV5HH6a9cALeeFSS4auVpx7l-s_7kXRGhsUZ5mG0M/exec";

const HomeSection6 = ({ id }: { id?: string }) => {
  const [status, setStatus] = useState("");
  const [loading, setLoading] = useState(false);
  const [selectedService, setSelectedService] = useState("");

  const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();

    const form = e.currentTarget;
    const formData = new FormData(form);

    setLoading(true);
    setStatus("Sending...");

    try {
      await fetch(GOOGLE_SCRIPT_URL, {
        method: "POST",
        mode: "no-cors",
        body: formData,
      });

      setStatus("Message sent successfully!");
      form.reset();
      setSelectedService("");
    } catch (error) {
      console.error(error);
      setStatus("Something went wrong. Please try again.");
    } finally {
      setLoading(false);
    }
  };

  return (
    <ResponsiveBox
      id={id}
      classNames="relative overflow-hidden bg-[#081120] py-14 md:py-20"
    >
      <div className="absolute right-0 top-0 h-[220px] w-[220px] rounded-full bg-[#FF6A00]/12 blur-[70px] md:h-[350px] md:w-[350px]" />
      <div className="absolute bottom-0 left-0 h-[220px] w-[220px] rounded-full bg-[#FF6A00]/6 blur-[70px] md:h-[300px] md:w-[300px]" />

      <ConstraintedBox classNames="relative z-10 px-4">
        <div className="mx-auto max-w-3xl text-center">
          <p className="mb-3 text-xs font-bold uppercase tracking-[0.25em] text-[#FF6A00] md:text-sm md:tracking-[0.35em]">
            Contact
          </p>

          <h2 className="text-3xl font-extrabold text-white md:text-5xl">
            Let&apos;s Build Something{" "}
            <span className="bg-gradient-to-r from-[#FF6A00] to-[#FFB06B] bg-clip-text text-transparent">
              Powerful
            </span>
          </h2>

          <p className="mx-auto mt-4 max-w-2xl text-sm leading-7 text-[#DCE8F8] md:text-base">
            Tell us about your project. We&apos;ll help turn your idea into a
            successful digital product.
          </p>
        </div>

        <form
          onSubmit={handleSubmit}
          className="contact-form mx-auto mt-8 max-w-4xl rounded-[1.5rem] border border-[#FF6A00]/25 bg-[#0B1628] p-4 md:mt-12 md:rounded-[2rem] md:p-8"
        >
          <div className="grid gap-4 md:grid-cols-2 md:gap-5">
            <input
              name="name"
              required
              placeholder="Full Name *"
              className="contact-input h-14 text-base md:h-16"
            />

            <input
              name="email"
              type="email"
              required
              placeholder="Email Address *"
              className="contact-input h-14 text-base md:h-16"
            />

            <input
              name="phone"
              placeholder="Phone Number"
              className="contact-input h-14 text-base md:h-16"
            />

            <select
              name="service"
              required
              value={selectedService}
              onChange={(e) => setSelectedService(e.target.value)}
              className={`contact-input h-14 text-base md:h-16 ${
                selectedService ? "text-white" : "text-[#8A93A5]"
              }`}
            >
              <option value="" disabled>
                Select Service *
              </option>
              <option value="Website Development">Website Development</option>
              <option value="Mobile App Development">
                Mobile App Development
              </option>
              <option value="Game Development">Game Development</option>
              <option value="Multiplayer Game Development">
                Multiplayer Game Development
              </option>
              <option value="Backend Development">Backend Development</option>
              <option value="UI / UX Design">UI / UX Design</option>
              <option value="Cloud & DevOps">Cloud & DevOps</option>
              <option value="AI & Automation">AI & Automation</option>
              <option value="E-Commerce Solutions">E-Commerce Solutions</option>
              <option value="Custom Software Development">
                Custom Software Development
              </option>
              <option value="Software Consulting">Software Consulting</option>
            </select>
          </div>

          <textarea
            name="message"
            required
            rows={5}
            placeholder="Tell us about your project *"
            className="contact-input mt-4 w-full resize-none text-base md:mt-5"
          />

          <button
            type="submit"
            disabled={loading}
            className="mt-5 w-full rounded-xl bg-[#FF6A00] px-8 py-4 font-bold text-white transition-all duration-300 hover:bg-[#FF8124] hover:shadow-[0_0_25px_rgba(255,106,0,0.4)] disabled:opacity-60 md:w-auto"
          >
            {loading ? "Sending..." : "Send Message"}
          </button>

          {status && (
            <p className="mt-4 text-sm font-semibold text-[#FF8124]">
              {status}
            </p>
          )}
        </form>
      </ConstraintedBox>
    </ResponsiveBox>
  );
};

export default HomeSection6;