import Link from "next/link";
import Column from "@/components/core/Column";

const FooterContactInfo = () => {
  return (
    <Column classNames="gap-5">
      <p className="text-sm font-bold uppercase tracking-[0.3em] text-[#FF6A00]">
        Contact
      </p>

      <div className="grid gap-3">
        <div className="footer-contact-card rounded-2xl border border-[#FF6A00]/15 bg-[#050D1B]/70 p-4">
          <p className="font-bold text-white">Address</p>
          <p className="mt-1 text-[#DCE8F8]">
            Alangulam, Tirunelveli - 627851
          </p>
        </div>

        <div className="footer-contact-card rounded-2xl border border-[#FF6A00]/15 bg-[#050D1B]/70 p-4">
          <p className="font-bold text-white">Email</p>
          <Link
            href="mailto:Nagaraja@semraa.com"
            className="mt-1 block text-[#DCE8F8] hover:text-[#FF6A00]"
          >
            Nagaraja@semraa.com
          </Link>
        </div>

        <div className="footer-contact-card rounded-2xl border border-[#FF6A00]/15 bg-[#050D1B]/70 p-4">
          <p className="font-bold text-white">Working Hours</p>
          <p className="mt-1 text-[#DCE8F8]">
            Monday - Friday: 10:00 AM - 7:00 PM
          </p>
          <p className="text-[#DCE8F8]">Saturday & Sunday: Off</p>
        </div>
      </div>
    </Column>
  );
};

export default FooterContactInfo;