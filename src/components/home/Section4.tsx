import ResponsiveBox from "@/components/core/ResponsiveBox";
import ConstraintedBox from "@/components/core/ConstraintedBox";
import skills from "@/data/skills";
import SkillItem from "./components/SkillItem";

const HomeSection4 = ({ id }: { id?: string }) => {
  return (
    <ResponsiveBox
      id={id}
      classNames="relative overflow-hidden bg-[#081120] py-20"
    >
      <ConstraintedBox classNames="relative z-10 px-4">
        {/* Section Header */}
        <div className="mb-16 flex flex-col items-center justify-center text-center w-full">
          <p className="text-sm font-semibold uppercase tracking-[0.25em] text-[#FF6A00]">
            OUR SKILLS
          </p>

          <h2 className="mt-4 text-3xl md:text-5xl font-bold text-white text-center">
            Technologies & Services
          </h2>

          <p className="mt-5 max-w-3xl text-center text-white/70 text-base md:text-lg">
            Leveraging modern technologies and industry best practices to
            deliver scalable software solutions, cloud platforms, web
            applications, and game development services.
          </p>

          <div className="mt-6 h-1 w-24 rounded-full bg-[#FF6A00]" />
        </div>

        {/* Skills Grid */}
        <div className="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
          {skills.map((skillGroup, index) => (
            <SkillItem
              key={`skill-group-${index}`}
              data={skillGroup}
            />
          ))}
        </div>
      </ConstraintedBox>
    </ResponsiveBox>
  );
};

export default HomeSection4;