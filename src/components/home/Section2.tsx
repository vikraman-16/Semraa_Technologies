import ConstraintedBox from "@/components/core/ConstraintedBox";
import ResponsiveBox from "@/components/core/ResponsiveBox";
import GridBox from "@/components/core/GridBox";
import ServiceItem from "./components/ServiceItem";
import services from "@/data/services";

const HomeSection2 = ({ id }: { id?: string }) => {
  return (
    <ResponsiveBox
      id={id}
      classNames="
        relative overflow-hidden
        bg-[#081120]
        min-h-[calc(100vh-5rem)]
        items-center justify-center
      "
    >
      {/* Lightweight Background Glow */}

      <div className="absolute top-0 right-0 h-[300px] w-[300px] rounded-full bg-[#FF6A00]/10 blur-[60px]" />

      <div className="absolute bottom-0 left-0 h-[300px] w-[300px] rounded-full bg-[#FF6A00]/5 blur-[60px]" />

      <ConstraintedBox classNames="relative z-10 px-4 py-20">
        {/* HEADER */}

        <div className="mx-auto max-w-5xl text-center">
          <div className="mx-auto mb-6 flex w-fit items-center gap-3 rounded-full border border-[#FF6A00]/20 bg-[#FF6A00]/10 px-5 py-2">
            <span className="h-2 w-2 rounded-full bg-[#FF6A00]" />

            <p className="text-xs font-bold uppercase tracking-[0.35em] text-[#FF8124]">
              WHAT WE DO
            </p>
          </div>

          <h2 className="text-4xl/tight md:text-5xl/tight xl:text-6xl/tight font-extrabold text-white">
            Technology Services For{" "}
            <span className="bg-gradient-to-r from-[#FF6A00] to-[#FFB06B] bg-clip-text text-transparent">
              Business Growth
            </span>
          </h2>

          <p className="mx-auto mt-6 max-w-3xl text-base md:text-lg leading-8 text-[#DCE8F8]">
            We deliver end-to-end software development services, from modern
            websites and mobile applications to scalable backend systems,
            cloud solutions and digital transformation.
          </p>
        </div>

        {/* SERVICES */}

        <GridBox classNames="justify-items-center sm:grid-cols-2 lg:grid-cols-3 mt-16 gap-6">
          {services.map((service, index) => (
            <div
              key={`service-${index}`}
              className="
                group
                w-full
                h-full
                rounded-[24px]
                border
                border-[#FF6A00]/15
                bg-[#0B1628]
                transition-all
                duration-300
                hover:-translate-y-1
                hover:border-[#FF6A00]/50
                hover:shadow-[0_0_20px_rgba(255,106,0,0.18)]
              "
            >
              <ServiceItem data={service} index={index} />
            </div>
          ))}
        </GridBox>
      </ConstraintedBox>
    </ResponsiveBox>
  );
};

export default HomeSection2;