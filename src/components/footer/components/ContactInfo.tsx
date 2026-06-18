const ContactInfo = () => {
  return (
    <div className="flex flex-col gap-5 text-white">
      <h3 className="text-xl font-bold text-[#FF6A00]">
        Get In Touch
      </h3>

      <div className="flex flex-col gap-4 text-[#DCE8F8]">
        <div>
          <p className="text-xs uppercase tracking-wider text-[#FF8124]">
            Email
          </p>
          <p className="mt-1">semraaastra@outlook.com</p>
        </div>

        {/* <div>
          <p className="text-xs uppercase tracking-wider text-[#FF8124]">
            Website
          </p>
          <p className="mt-1">https://semraa-technologies-kyo6-g05ye9nuy-semraa.vercel.app/</p>
        </div> */}

        <div>
          <p className="text-xs uppercase tracking-wider text-[#FF8124]">
            Location
          </p>
          <p className="mt-1">Tamil Nadu, India</p>
        </div>

        <div>
          <p className="text-xs uppercase tracking-wider text-[#FF8124]">
            Working Hours
          </p>
          <p className="mt-1">Monday - Saturday</p>
        </div>
      </div>
    </div>
  );
};

export default ContactInfo;