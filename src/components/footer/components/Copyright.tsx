import Link from "next/link";
import Column from "@/components/core/Column";

const FooterCopyright = () => {
  return (
    <Column classNames="mt-10 items-center justify-center border-t border-[#FF6A00]/15 pt-6">
      <p className="text-center text-sm text-[#B7C3D4]">
        Copyright © 2021-2026{" "}
        <Link href="/" className="font-semibold text-[#FF6A00] hover:underline">
          Semraa Technologies
        </Link>
        . All Rights Reserved.
      </p>
    </Column>
  );
};

export default FooterCopyright;