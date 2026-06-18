import type { ISkillListItem } from "@/types";

import CardBox from "@/components/core/CardBox";
import Row from "@/components/core/Row";
import Column from "@/components/core/Column";

const getIcon = (title: string) => {
  switch (title) {
    // Software Development
    case "Python":
      return "🐍";
    case ".NET":
      return "🔷";
    case "C#":
      return "🎯";
    case "JavaScript":
      return "🟨";
    case "TypeScript":
      return "🔵";
    case "PHP":
      return "🐘";

    // Frontend
    case "Angular":
      return "🅰️";
    case "React":
      return "⚛️";
    case "Next.js":
      return "▲";
    case "HTML":
      return "🧱";
    case "CSS":
      return "🎨";

    // Backend
    case "Node.js":
      return "🟢";
    case "Express":
      return "🚀";
    case "REST APIs":
      return "🔌";

    // Cloud
    case "Azure":
      return "☁️";
    case "AWS":
      return "🌩️";
    case "GCP":
      return "🌤️";
    case "Firebase":
      return "🔥";

    // Database
    case "MongoDB":
      return "🍃";
    case "MySQL":
      return "🗄️";

    // Game Development
    case "Unity":
      return "🎮";
    case "Android Games":
      return "📱";
    case "PC Games":
      return "💻";
    case "WebGL Games":
      return "🌐";

    // Tools
    case "Git":
      return "🔀";
    case "GitHub":
      return "🐙";
    case "VS Code":
      return "💻";
    case "Ubuntu":
      return "🐧";

    // Marketing
    case "SEO":
      return "📈";
    case "Online Marketing":
      return "📢";

    // Creative
    case "Video Editing":
      return "🎬";
    case "2D Animation":
      return "🎨";
    case "3D Animation":
      return "🧊";
    case "Ad Film Making":
      return "🎥";

    default:
      return "⭐";
  }
};

const SkillItem = ({ data }: { data: ISkillListItem }) => {
  return (
    <CardBox classNames="w-full rounded-2xl overflow-hidden bg-[#111827] border border-white/10">
      {/* Header */}
      <div className="bg-[#FF6A00] px-4 py-3">
        <h3 className="text-center text-white font-bold text-lg">
          {data.title}
        </h3>
      </div>

      {/* Skills */}
      <div className="p-6">
        <Row classNames="gap-5 flex-wrap justify-center items-start">
          {data.items.map((skill, index) => (
            <Column
              key={index}
              classNames="items-center gap-2 w-24"
            >
              <div className="w-16 h-16 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-3xl">
                {getIcon(skill.title)}
              </div>

              <p className="text-xs text-center text-white leading-tight">
                {skill.title}
              </p>
            </Column>
          ))}
        </Row>
      </div>
    </CardBox>
  );
};

export default SkillItem;